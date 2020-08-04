<?php
use Bitrix\Main\Page\Asset;

require_once __DIR__ . '/../vendor/autoload.php';

CJSCore::RegisterExt('edu7',
    array(
        'js' => '/local/js/edu7.js',
        // 'lang' => '/local/lang/' . LANGUAGE_ID . '/custom.js.php',
        // 'css' => '/local/css/custom_stuff.css',
        // 'rel' => array(
        //     'ajax',
        //     'popup'
        // )
    )
);

CJSCore::Init(['edu7', 'jquery2']);

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

require_once __DIR__ . '/include/register_usertype.php';