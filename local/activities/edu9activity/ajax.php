<?php
$_SERVER['DOCUMENT_ROOT'] = realpath(__DIR__ . '/../../../');

require_once ($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

\Bitrix\Main\Loader::includeModule('crm');

echo "<tr><td>HI THERE</td></tr>";