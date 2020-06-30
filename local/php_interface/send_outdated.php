<?php
use Bitrix\Main\Mail\Event;

require_once "./bootstrap.php";

class SendOutdated extends DmitryTestBase
{
    const OUTDATED_FILTER = '-1 day';

    public function __invoke()
    {
        // Получить список лидов, где последнее изменение производилось N-ое время назад
        $leads = CCrmLead::GetList(
            [
                'STATUS_ID' => 'DESC',
            ],
            [
                'CHECK_PERMISSIONS' => 'N',
                '<DATE_MODIFY'      => \Bitrix\Main\Type\DateTime::createFromTimestamp(strtotime(self::OUTDATED_FILTER))->toString(),
                // Последнее изменение 24 часа назад
                'STATUS_ID'         => [
                    self::CRM_STATUS_ID_NEW,
                    self::CRM_STATUS_ID_INTERESTED,
                ]
            ]
        );

        $arLeads = [];

        // Получить доп. детали по каждому лиду
        while ($arLead = $leads->Fetch())
        {
            $arLeads[] = $this->assembleLead($arLead);
        }

        if ($arLeads)
        {
            $arGroupedLeads = $this->groupLeadsBy($arLeads, self::FIELD_OWNER);

            foreach ($arGroupedLeads as $arGrouped)
            {
                $this->sendEmailToOwner($arGrouped);
                $this->sendEmailToManager($arGrouped);
            }
        }

        echo "Done\n";
    }

    /**
     * Группировать по ответственному за лид
     *
     * @param $arLeads
     * @param $field
     * @return array
     */
    public function groupLeadsBy($arLeads, $field)
    {
        $grouped = [];

        foreach ($arLeads as $lead)
        {
            if (!isset($grouped[$lead[$field]]))
            {
                $grouped[$lead[$field]] = [];
            }

            $grouped[$lead[$field]][] = $lead;
        }

        return array_values($grouped);
    }

    /**
     * Получить доп. детали для каждого лида
     *
     * @param $arLead
     * @return mixed
     */
    public function assembleLead($arLead)
    {
        $arLead['DURATION'] = $this->getStageDuration($arLead['ID']);

        return $arLead;
    }

    /**
     * Получить длительность нахождения лида в последнее стадии в днях
     *
     * @param $leadId
     * @return int|null
     */
    public function getStageDuration($leadId)
    {
        // , указав в фильтре ENTITY_ID, ENTITY_TYPE и ENTITY_FIELD
        $events = CCrmEvent::GetList(
            [
                'DATE_CREATE' => 'DESC',
            ],
            [
                'CHECK_PERMISSIONS' => 'N',
                'ENTITY_ID' => $leadId,
                'ENTITY_TYPE' => 'LEAD',
                'ENTITY_FIELD' => 'STATUS_ID',
            ]
        );

        $last = $prev = null;

        while ($event = $events->Fetch())
        {
            if (is_null($last))
            {
                $last = \Bitrix\Main\Type\DateTime::tryParse($event['DATE_CREATE']);
            } elseif (is_null($prev))
            {
                $prev = \Bitrix\Main\Type\DateTime::tryParse($event['DATE_CREATE']);
            } else
            {
                break;
            }
        }

        if ($last && $prev)
        {
            return (int) $last->getDiff($prev)->days;
        }

        return null;
    }

    /**
     * Послать письмо руководителю ответственного за лид
     *
     * @param mixed[] $arLeads - список лидов
     */
    public function sendEmailToManager($arLeads)
    {
        $userId = $arLeads[0][self::FIELD_OWNER];
        $managerUserId = $this->getUserManager($userId);

        if (!$managerUserId)
        {
            return;
        }

        $user = $this->getUser($userId);
        $email = $this->getUserEmail($managerUserId);

        $message = $this->assembleMessage(
            $arLeads,
            "Некоторые лиды ваших сотрудников долго не двигались по воронке.
            Убедитесь, что были предприняты достаточные усилия, уточните причины невозможности продвижения лида на
            последующие стадии.<p></p><div>" . $this->toUtf($user['NAME']) . "</div>");

        $this->sendEmaiLWrapper([
            'EVENT_NAME' => 'EMAIL_FORM',
            'LID'        => 's1',
            'C_FIELDS'   => [
                'MESSAGE' => $message,
                'EMAIL'   => $email,
                'USER_ID' => $managerUserId,
            ],
        ]);
    }

    /**
     * Послать письмо ответственному за лид
     *
     * @param $arLeads
     */
    public function sendEmailToOwner($arLeads)
    {
        $userId = $arLeads[0][self::FIELD_OWNER];
        $email = $this->getUserEmail($userId);

        $message = $this->assembleMessage(
            $arLeads,
            "Некоторые ваши лиды долго не двигались по воронке. Убедитесь, что были предприняты достаточные
            усилия, оцените дальнейшие перспективы: если их нет — закройте лиды."
        );

        $this->sendEmaiLWrapper([
            'EVENT_NAME' => 'EMAIL_FORM',
            'LID'        => 's1',
            'C_FIELDS'   => [
                'MESSAGE' => $message,
                'EMAIL'   => $email,
                'USER_ID' => $userId,
            ],
        ]);
    }

    /**
     * Текст письма
     *
     * @param mixed[] $arLeads - массив с лидами
     * @param $message - сообщение
     * @return string - текст сообщения
     */
    public function assembleMessage($arLeads, $message)
    {
        $html = <<<EOT
<div>{$message}</div>
<table>
<tbody>

EOT;

        foreach ($arLeads as $lead)
        {
            $html .= "<tr>";
            $html .= "<td><a href='{$this->getLeadUrl($lead['ID'])}'>{$this->toUtf($lead['TITLE'])}</a></td>";
            $html .= "<td>{$this->leadStatusToText($lead['STATUS_ID'])}</td>";
            $html .= "<td>{$lead['DURATION']} day(s)</td>";
            $html .= "</tr>\n";
        }

        $html .= <<<EOT
</tbody>
</table>
EOT;

        return $html;
    }
}

(new SendOutdated())();

require_once "./shutdown.php";

