<?php
use Itech\Bitrix\Model\CRM\Deal;

$_SERVER['DOCUMENT_ROOT'] = __DIR__;

require_once $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';

// подключение пролога
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

\Bitrix\Main\Loader::includeModule('crm');

/** @var CAllMain $APPLICATION */
$APPLICATION->IncludeComponent(
    "task3:deal_tasks",
    ".default",
    Array(
        'ID' => $_GET['id'],
    ),
    false
);

// подключение эпилога
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
