<?php
CModule::AddAutoloadClasses(
    'dmitry.edu8',
    [
        'Dmitry\Edu8\Main'         => 'lib/main.php',
        'Dmitry\Edu8\CCityElement' => 'lib/field.php',
    ]
);

CJSCore::Init(['jquery2']);
