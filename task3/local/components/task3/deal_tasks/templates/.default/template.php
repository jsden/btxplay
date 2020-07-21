<?php
/** @var \Itech\Bitrix\Model\CRM\Deal $deal */
$deal = $arResult['DEAL'];

$activity = $deal->getActivity();

$rows = array_map(
    function ($item)
    {
        return [
            'data' => [
                'TYPE_NAME' => $item->TYPE_NAME,
                'DEADLINE'  => $item->DEADLINE,
            ]
        ];
    },
    $activity
);

$APPLICATION->IncludeComponent('bitrix:main.ui.filter', '', [
    'FILTER_ID' => 'report_list',
    'GRID_ID' => 'DEAL_TASKS',
    'FILTER' => [
        ['id' => 'TYPE_NAME', 'name' => 'Дело', 'type' => 'string'],
        ['id' => 'DEADLINE', 'name' => 'Дедлайн', 'type' => 'date'],
    ],
    'ENABLE_LIVE_SEARCH' => FALSE,
    'ENABLE_LABEL' => true
]);

//вызовем компонент грида для отображения данных
$APPLICATION->IncludeComponent(
    "bitrix:main.ui.grid",
    "",
    array(
//уникальный идентификатор грида
"GRID_ID"           => "DEAL_TASKS",
//описание колонок грида, поля типизированы
"HEADERS"           => array(
    array("id" => "TYPE_NAME", "name" => "Дело", "sort" => "TYPE_NAME", "default" => true, "editable" => false),
    array("id" => "DEADLINE", "name" => "Дедлайн", "sort" => "DEADLINE", "default" => true, "editable" => true),
),
//сортировка
"SORT"              => $arResult["SORT"],
//это необязательный
"SORT_VARS"         => $arResult["SORT_VARS"],
//данные
"ROWS"              => $rows,
//объект постранички
"NAV_OBJECT"        => $arResult["NAV_OBJECT"],
//можно использовать в режиме ajax
"AJAX_MODE"         => "Y",
"AJAX_OPTION_JUMP"  => "N",
"AJAX_OPTION_STYLE" => "Y",
//фильтр
"FILTER"            => $arResult["FILTER"],
    ),
    $component
);