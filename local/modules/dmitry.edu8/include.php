<?php
CModule::AddAutoloadClasses(
    'dmitry.edu8',
    [
        'Dmitry\Edu8\Main'         => 'lib/main.php',
        'Dmitry\Edu8\CCityElement' => 'lib/field.php',
    ]
);

CJSCore::RegisterExt(
    'Edu8',
    [
        'js'   => '/local/modules/dmitry.edu8/lib/js/edu8.js',
        'rel'  => ['jquery2'],
    ]
);

CJSCore::Init(['jquery2', 'Edu8']);