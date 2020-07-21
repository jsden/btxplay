<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Регистрируем обработчик события
\Bitrix\Main\EventManager::getInstance()->addEventHandler('main', 'onProlog', function () {
        // Проверим является ли страница детальной карточкой CRM через функционал роутинга компонентов
        $engine = new \CComponentEngine();
        $page = $engine->guessComponentPath(
            '/crm/',
            ['detail' => '#entity_type#/details/#entity_id#/'],
            $variables
        );


        // Если страница не является детальной карточкой CRM прервем выполенение
        if ($page !== 'detail') {
            return;
        }

        // Проверим валидность типа сущности
        $allowTypes = ['lead', 'deal', 'contact', 'company'];
        $variables['entity_type'] = strtolower($variables['entity_type']);
        if (!in_array($variables['entity_type'], $allowTypes, true)) {
            return;
        }

        // Проверим валидность идентификатора сущности
        $variables['entity_id'] = (int) $variables['entity_id'];
        if (0 >= $variables['entity_id']) {
            return;
        }

        $assetManager = \Bitrix\Main\Page\Asset::getInstance();

        // Подключаем js файл
        $assetManager->addJs('/local/components/task3/deal_tasks/app.js');

        $jsParams = \Bitrix\Main\Web\Json::encode(
            $variables,
            JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
        );

        // Инициализируем добавление таба
        $assetManager->addString('
        <script>
        BX.ready(function () {
            if (typeof initialize_foo_crm_detail_tab === "function") {
                initialize_foo_crm_detail_tab('.$jsParams.');
            }
        });
        </script>
    ');
    });