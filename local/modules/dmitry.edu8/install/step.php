<?php
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if (!check_bitrix_sessid())
{

    return;
}

/** @var CAllMain $APPLICATION */
if ($errorException = $APPLICATION->GetException())
{
    /* Ахахаха, лол */
    CAdminMessage::ShowMessage($errorException->GetString());
} else
{
    /* Вызов метода как статик, лол */
    CAdminMessage::ShowNote(
            Loc::getMessage('EDU8_STEP_BEFORE')
            . ' '
            . Loc::getMessage('EDU8_STEP_AFTER'));
}
?>

<form action="<? echo $APPLICATION->GetCurPage(); ?>">
    <input type="hidden" name="lang" value="<? echo(LANG); ?>"/>
    <input type="submit" value="<? echo(Loc::getMessage('EDU8_STEP_SUBMIT_BACK')); ?>">
</form>
