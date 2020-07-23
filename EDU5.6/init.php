<?php
require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/include/events/CRMDealEvent.php';

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

$edu5 = new CRMDealEvent();

AddEventHandler(
    'crm',
    'OnBeforeCrmDealUpdate',
    [$edu5, 'onBeforeCrmDealUpdate'],
    10000
);