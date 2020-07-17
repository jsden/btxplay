<?php
use Itech\Bitrix\Model\CRM\Deal;

/**
 * Что НЕ сделано или сделано неправильно
 * 1. Не сделана передача фильтров для списков сущностей (объекты вместо массивов)
 * 2. Список активити не кэшируется для сделки
 * 3. Оптимально будет получить список активити для всех итоговых сделок вместо получения
 *      для каждой сделки в отдельности
 * 4. Работа с датами - я использовал strtotime - подозреваю что там должен использоваться DateTime
 * 5. Я делаю библиотеку для облегчения работы. Наверное это не айс, и возможно никто кроме меня ей
 *      пользоваться не будет, но на мой взгляд это сильно упростит работу в конечном итоге. Сейчас куча
 *      кода в статик методах, отсутствует работа с коллекциями, и т.д., и т.п.
 * 6. Необходимо добавить поддержку коллекций (и, возможно, генераторов, но это будет позже) для списков
 *      сущностей вместо массивов.
 * 7. + так по мелочи, в моей голове
 */

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';

// подключение пролога
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

\Bitrix\Main\Loader::includeModule('crm');

$userId = CCrmSecurityHelper::GetCurrentUserID();

$deals = new \Itech\Bitrix\Repo\CRM\DealRepo();

/** @var \Itech\Bitrix\Model\CRM\Deal[] $list */
$list = $deals->getList(
    [],
    [
        'ASSIGNED_BY_ID' => $userId
    ]
);

// Filter'em up
$list = array_filter(
    $list,
    function ($item)
    {
        /** @var Deal $item */

        /* All activities */
        // return true;

        /* Only with activities */
        // return (bool) $item->getActivity();

        /* With no activities or with expired activities */
        $activities = $item->getActivity();
        if (!$activities)
        {
            return true;
        }
        foreach ($activities as $activity)
        {
            /** @var \Itech\Bitrix\Model\CRM\Activity $activity */
            if (strtotime($activity->DEADLINE) < time())
            {
                return true;
            }
        }

        return false;
    }
);

echo <<<EOT
<table border="1" cellpadding="4" cellspacing="0">'
<thead>
<tr>
    <th>ID</th>
    <th>Стадия</th>
    <th>Дела</th>
    <th>Клиент</th>
    <th>Сумма</th>    
</tr>
</thead>
<tbody>
EOT;

foreach ($list as $item)
{
    echo "<tr>";
    echo "<td>{$item->ID}</td>";
    echo "<td>{$item->STAGE_ID}</td>";
    echo "<td>";
    echo implode(
        "<br>",
        array_map(
            function($item)
            {
                /** @var \Itech\Bitrix\Model\CRM\Activity */
                return $item->TYPE_NAME . " => " . $item->DEADLINE;
            },
            $item->getActivity()
        )
    );
    echo "</td>";
    echo "<td>{$item->CONTACT_FULL_NAME}</td>";
    echo "<td>{$item->OPPORTUNITY}&nbsp;{$item->CURRENCY_ID}</td>";
    echo "</tr>";
}

echo <<<EOT
</tbody>
</table>
EOT;


// подключение эпилога
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
