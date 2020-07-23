<?php
require_once __DIR__ . '/../vendor/autoload.php';

class EDU5
{
    public function onBeforeCrmDealUpdate(&$arFields)
    {
        if (!isset($arFields['ASSIGNED_BY_ID']))
            return;

        $dealRepo = new \Itech\Bitrix\Repo\CRM\DealRepo();
        /* @var \Itech\Bitrix\Model\CRM\Deal $deal */
        $deal = $dealRepo->getById($arFields['ID']);

        if (!$deal || ($deal->ASSIGNED_BY_ID == $arFields['ASSIGNED_BY_ID']))
            return;

        $contactRepo = new \Itech\Bitrix\Repo\CRM\ContactRepo();

        $prevUser = $contactRepo->getById($deal->ASSIGNED_BY_ID);
        $newUser = $contactRepo->getById($arFields['ASSIGNED_BY_ID']);

        if ($prevUser && $newUser)
        {
            $this->notifyUser(
                $prevUser,
                $newUser,
                $deal,
                "Сделка %s переведена на нового Ответственного: %s"
            );

            $this->notifyUser(
                $newUser,
                $prevUser,
                $deal,
                "Вам назначена сделка %s пользователем %s"
            );
        }
    }

    public function notifyUser($toUser, $fromUser, $deal, $text)
    {
        $message = sprintf($text, $deal->TITLE, $fromUser->NAME);

        $notification = (new \Itech\Bitrix\Service\CIMMessageBuilder())
            ->to($toUser)
            ->from($fromUser)
            ->type(\Itech\Bitrix\Model\CIMMessage::IM_NOTIFY_SYSTEM)
            ->title($message)
            ->get();

        // Send notification
        $notificationService = new \Itech\Bitrix\Service\NotificationService();
        $notificationService->send($notification);
    }
}

$edu5 = new EDU5;

AddEventHandler(
    'crm',
    'OnBeforeCrmDealUpdate',
    [$edu5, 'onBeforeCrmDealUpdate'],
    10000
);
