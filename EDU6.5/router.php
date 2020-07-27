<?php
$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';

// подключение пролога
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

\Bitrix\Main\Loader::includeModule('crm');

switch ($_POST['mode'])
{
    case 'update_deal_comment':
        // Открыть репу
        $dealRepo = new \Itech\Bitrix\Repo\CRM\DealRepo();

        // Найти сделку
        if ($deal = $dealRepo->getById($_POST['id'])) {
            // Записать коммент
            $deal->UF_CRM_1594815549263 = $_POST['comment'];

            // Сохранить изменения
            $dealRepo->update($deal);
        }

        break;
}