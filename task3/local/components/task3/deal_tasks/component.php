<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
{
    die();
}

/** @var CDealTasks $this */

$arResult['GRID_ID'] = 'deal_tasks';

$arResult["FILTER"] = array(
    ['id' => 'TYPE_NAME', 'name' => 'Дело', 'type' => 'string'],
);

// Применить фильтр тут
$arResult["ROWS"] = $this->getTasks(
    $arParams['ID'],
    new \Bitrix\Main\UI\Filter\Options($arResult['GRID_ID']),
    $arResult['FILTER']
);

$this->includeComponentTemplate();