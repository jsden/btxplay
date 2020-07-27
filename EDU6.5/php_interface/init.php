<?php
use Bitrix\Main\Page\Asset;

require_once __DIR__ . '/../vendor/autoload.php';

CJSCore::RegisterExt('custom',
    array(
        'js' => '/local/js/custom.js',
        'lang' => '/local/lang/' . LANGUAGE_ID . '/custom.js.php',
        // 'css' => '/local/css/custom_stuff.css',
        'rel' => array(
            'ajax',
            'popup'
        )
    )
);

CJSCore::Init(['custom', 'jquery2']);

$asset = Asset::getInstance();
$asset->addString('<script>BX.ready(function () { BX.Custom.bindEvents(); });</script>');