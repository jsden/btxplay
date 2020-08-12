<?php
defined('B_PROLOG_INCLUDED') || die();

use Bitrix\Bizproc\FieldType;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$arActivityDescription = array(
    // Название действия для конструтора.
    'NAME'              => Loc::getMessage('EDU9_ACTIVITY_NAME'),

    // Описание действия для конструктора.
    'DESCRIPTION'       => Loc::getMessage('EDU9_ACTIVITY_DESCR'),

    // Тип: “activity” - действие, “condition” - ветка составного действия.
    'TYPE'              => 'activity',

    // Название класса действия без префикса “CBP”.
    'CLASS'             => 'Edu9Activity',

    // Название JS-класса для управления внешним видом и поведением в конструкторе.
    // Если нужно только стандартное поведение, указывайте “BizProcActivity”.
    'JSCLASS'           => 'BizProcActivity',

    // Категория действия в конструкторе.
    // Данный шаг размещен в категории “Уведомления” по историческим причинам.
    'CATEGORY'          => array(
        'ID' => 'interaction',
    ),
    // Нзвания свойств действия, из которых будут взяты возвращаемые значения.
    'ADDITIONAL_RESULT' => array('QuestionnaireResults')
);