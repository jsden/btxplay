<?php
$_SERVER["DOCUMENT_ROOT"] = realpath(dirname(__FILE__) . "/../..");
$DOCUMENT_ROOT = $_SERVER["DOCUMENT_ROOT"];

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("NO_AGENT_CHECK", true);
// define('LID', "s1");
// define("LANG", "ru");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("crm");

class DmitryTestBase
{
    const CRM_STATUS_ID_NEW = 'NEW';
    const CRM_STATUS_ID_INTERESTED = 1;

    const CREATOR_ID = 1;

    const CREATION_METHOD = 'UF_CRM_1592576527';
    const CREATION_METHOD_SCRIPT = 27;

    const FIELD_PHONE = 'PHONE';
    const FIELD_EMAIL = 'EMAIL';
    const FIELD_SOURCE = 'SOURCE';

    protected function toUtf($message)
    {
        return iconv("windows-1251", "utf-8", $message);
    }

    protected function fromUtf($message)
    {
        return iconv("utf-8", "windows-1251", $message);
    }

    protected function error($message)
    {
        echo $this->toUtf($message) . "\n";
    }

    protected function info($message)
    {
        echo $message . "\n";
    }

    /**
     *
     * @return CDBResult
     */
    protected function getContacts()
    {
        return CCrmContact::GetListEx(
            [],
            [
                'TYPE_ID'           => 'CLIENT',
                'CHECK_PERMISSIONS' => 'N',
            ]
        );
    }

    protected function fetchMulti($id, $field)
    {
        $arFilter = [
            'ENTITY_ID'  => 'CONTACT',
            'ELEMENT_ID' => $id,
            'TYPE_ID'    => $field,
            'VALUE_TYPE' => 'WORK',
        ];

        $arValue = \CCrmFieldMulti::GetListEx([], $arFilter, false, ['nTopCount' => 1], ['VALUE'])->fetch();

        return (is_array($arValue) && (!empty($arValue))) ? $arValue['VALUE'] : '';
    }

    protected function leadStatusToText($statusId)
    {
        $allStatuses = [
            self::CRM_STATUS_ID_INTERESTED => 'Заинтересован',
            self::CRM_STATUS_ID_NEW => 'Не обработан',
        ];

        return $allStatuses[$statusId];
    }
}