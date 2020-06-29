<?php
require_once "./bootstrap.php";

class SendEmails extends DmitryTestBase
{
    public function __invoke()
    {
        // Получить список контактов
        $arContacts = $this->getContacts();

        while ($arContact = $arContacts->Fetch())
        {
            $this->sendFormLink($arContact);
        }
    }

    /**
     * Послать ссылку на CRM форму
     *
     * @param $arContact
     */
    public function sendFormLink($arContact)
    {
        $email = $this->getContactEmail($arContact['ID']);

        $arEventFields = [
            'EMAIL'   => $email,
            'USER_ID' => $arContact['ID'],
        ];
        $result = CEvent::Send('EMAIL_FORM', 's1', $arEventFields, 'N', 1);

        if ($result)
        {
            $this->info("Шлём письмо на {$email}");
        } else
        {
            $this->error("Ошибка отсылки почты на {$email}");
        }
    }
}

(new SendEmails)();

require_once "./shutdown.php";
