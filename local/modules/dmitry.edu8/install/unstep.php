<?php
use Bitrix\Main\Localization\Loc;

/** @var CAllMain $APPLICATION */

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid())
{

    return;
}

CAdminMessage::ShowNote(Loc::getMessage("EDU8_UNSTEP_BEFORE") . " " .
    Loc::getMessage("EDU8_UNSTEP_AFTER"));
?>

<form action="<? echo($APPLICATION->GetCurPage()); ?>">
    <input type="hidden" name="lang" value="<? echo(LANG); ?>"/>
    <input type="submit" value="<? echo(Loc::getMessage('EDU8_UNSTEP_SUBMIT_BACK')); ?>">
</form>
