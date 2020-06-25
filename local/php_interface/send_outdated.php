<?php
use Bitrix\Main\Mail\Event;

require_once "./bootstrap.php";

class SendOutdated extends DmitryTestBase
{
    const OUTDATED_FILTER = '-1 day';

    public function __invoke()
    {
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

        while ($arLead = $leads->Fetch())
        {
            $this->sendEmailToOwner($arLead);
            $this->sendEmailToResponsible($arLead);
        }

        exit;
    }

    public function sendEmailToResponsible($arLead)
    {
        $email = $userId = $contactName = ''; // TODO

        $message = $this->assembleMessage(
            $arLead,
            "Некоторые лиды ваших сотрудников долго не двигались по воронке.
            Убедитесь, что были предприняты достаточные усилия, уточните причины невозможности продвижения лида на
            последующие стадии.<div>{$contactName}</div>");

        Event::send([
            'EVENT_NAME' => 'EMAIL_FORM',
            'LID'        => 's1',
            'C_FIELDS'   => [
                'MESSAGE' => $message,
                'EMAIL'   => $email,
                'USER_ID' => $userId,
            ],
        ]);
    }

    public function sendEmailToOwner($arLead)
    {
        $email = $userId = ''; // TODO

        $message = $this->assembleMessage(
            $arLead,
            "Некоторые ваши лиды долго не двигались по воронке. Убедитесь, что были предприняты достаточные
            усилия, оцените дальнейшие перспективы: если их нет — закройте лиды."
        );

        Event::send([
            'EVENT_NAME' => 'EMAIL_FORM',
            'LID'        => 's1',
            'C_FIELDS'   => [
                'MESSAGE' => $message,
                'EMAIL'   => $email,
                'USER_ID' => $userId,
            ],
        ]);
    }

    public function assembleMessage($arLeads, $message)
    {
        $html = <<<EOT
<div>{$message}</div>
<table>
<tbody>
EOT;

        foreach ($arLeads as $lead)
        {
            $link = ''; //
            $days = '';
            $status = $this->leadStatusToText($lead['STATUS_ID']);

            $html .= "<tr>";
            $html .= "<td>{$link}</td>";
            $html .= "<td>{$status}</td>";
            $html .= "<td>{$days}</td>";
            $html .= "</tr>";
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

