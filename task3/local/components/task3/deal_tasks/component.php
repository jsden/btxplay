<?php
if (check_bitrix_sessid() && $_SERVER['REQUEST_METHOD'] == "POST" && !empty($_REQUEST["error_message"]) && !empty($_REQUEST["error_url"]))
{
	/*
    $arMailFields = Array();
    $arMailFields["ERROR_MESSAGE"] = trim ($_REQUEST["error_message"]);
    $arMailFields["ERROR_DESCRIPTION"] = trim ($_REQUEST["error_desc"]);
    $arMailFields["ERROR_URL"] = $_REQUEST["error_url"];
    $arMailFields["ERROR_REFERER"] = $_REQUEST["error_referer"];
    $arMailFields["ERROR_USERAGENT"] = $_REQUEST["error_useragent"];

	CEvent::Send("BX", SITE_ID, $arMailFields);
	 */
}
$dealRepo = new \Itech\Bitrix\Repo\CRM\DealRepo();
$deal = $dealRepo->getById($arParams['ID']);

$arResult['DEAL'] = $deal;

$this->IncludeComponentTemplate();