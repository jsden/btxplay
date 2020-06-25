<?php
use Bitrix\Main\Mail\Event;

require_once "./bootstrap.php";

class SendEmails extends DmitryTestBase
{
    public function __invoke()
    {
        $arContacts = $this->getContacts();

        while ($arContact = $arContacts->Fetch())
        {
            $this->sendFormLink($arContact);
        }
    }

    public function assembleMessage($arContact)
    {
        return <<<EOT
<a href="http://bitrix/pub/form/3_registratsiya_na_vebinar/93pvqz/">Привет, {$arContact['FULL_NAME']}, нажмите на ссылку чтобы проиграть $100</a>
EOT;
    }

    public function sendFormLink($arContact)
    {
        $email = $this->fetchMulti($arContact['ID'], self::FIELD_EMAIL);

        $result = Event::send([
            'EVENT_NAME' => 'EMAIL_FORM',
            'LID'        => 's1',
            'C_FIELDS'   => [
                'MESSAGE' => $this->assembleMessage($arContact),
                'EMAIL'   => $email,
                'USER_ID' => $arContact['ID'],
            ],
        ]);

        if ($result->isSuccess())
        {
            $this->info("Шлём письмо на {$email}");
        } else
        {
            $this->error(implode(', ', $result->getErrorMessages()));
        }
    }
}

(new SendEmails)();

require_once "./shutdown.php";
