<?php
/** @var CAllMain $APPLICATION */
$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID'          => $arResult['GRID_ID'],
    'GRID_ID'            => $arResult['GRID_ID'],
    'FILTER'             => $arResult['FILTER'],
    'ENABLE_LIVE_SEARCH' => true,
    'ENABLE_LABEL'       => true,
    'DISABLE_SEARCH'     => true,
]);

$grid = (new \Itech\Bitrix\Component\Grid())
    ->setId($arResult['GRID_ID'])
    ->addHeader(
        [
            'id'       => 'TYPE_NAME',
            'name'     => 'Дело',
            'sort'     => 'TYPE_NAME',
            'default'  => true,
            'editable' => false
        ]
    )
    ->addHeader(
        [
            'id'       => 'DEADLINE',
            'name'     => 'Дедлайн',
            'sort'     => 'DEADLINE',
            'default'  => true,
            'editable' => true
        ]
    )
    ->setFilter($arResult['FILTER'])
    ->setRows($arResult['ROWS']);


//вызовем компонент грида для отображения данных
$APPLICATION->IncludeComponent(
    "bitrix:main.ui.grid",
    "",
    $grid->toArray(),
    $component
);