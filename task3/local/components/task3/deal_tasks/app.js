function initialize_foo_crm_detail_tab(params) {
    params = params || {};

    // Убедимся что тип сущности передан
    if  (!params.entity_type) {
        return;
    }

    // Убедимся что идентификатор сущности передан
    if  (!params.entity_id) {
        return;
    }

    // К сожалению менеджер карточки не генерирует никаких событий при инициализации
    // так что сложно определить когда он проинициализирован.
    // Поэтому будем проверять каждую секунду его наличие
    (new Promise(function (resolve, reject) {
        // Объявим переменную в которой будем хранить количество попыток
        var checkTabManagerCount = 0;

        // Объявим рекурсивную функцию для моиска менеджера карточки
        var checkTabManager = function () {
            // Если за 20 попыток не удалось найти менеджер карточки значит что-то пошло не так
            if (20 < ++checkTabManagerCount) {
                return reject();
            }

            // Сформируем идентификатор карточки
            var detailId = [params.entity_type, params.entity_id, 'details'].join('_');

            // Попробуем получить менеджер карточки по ее идентификатору
            var detailManager = BX.Crm.EntityDetailManager.items[detailId];

            // Если не получилось возможно это повторный лид или сделка
            if (!detailManager) {
                detailManager = BX.Crm.EntityDetailManager.items['returning_' + detailId];
            }

            // Если менеджер не найден значит он еще не проинициализирован
            if (!detailManager) {
                return setTimeout(checkTabManager, 1000);
            }

            // Успех вернем менеджер вкладок
            return resolve(detailManager._tabManager)
        };

        // Запустим поиск менеджера карточки
        checkTabManager();
    })).then(
        function (tabManager) {
            // Сформируем параметры вкладки
            var tabData = {};

            // Идентификатор вкладки
            tabData.id = 'lead_tasks';

            // Наименование вкладки
            tabData.name = 'Задачи';

            // Контент вкладки, если мы хотим чтобы во вкладке был статичный контент передаем его сюда, можно через параметры функции, в противном случае данный параметр можно опустить
            tabData.html = '<div>' + params.content + '</div>';

            // Создадим html узел отвечающий за контент вкладки
            var tabContainer = BX.create(
                'div',
                {
                    attrs: {
                        className: 'crm-entity-section crm-entity-section-info crm-entity-section-above-overlay crm-entity-section-tab-content-hide',
                    },
                    dataset: {
                        'tabId': tabData.id,
                    },
                    html: tabData.html,
                }
            );

            // Добавим созданный контейнер к остальным контейнерам вкладок
            BX.append(
                tabContainer,
                tabManager._container
            );

            // Создадим html узел отвечающий за кнопку вкладки в меню навигации карточки
            var tabMenuContainer = BX.create(
                'div',
                {
                    attrs: {
                        className: 'crm-entity-section-tab',
                    },
                    dataset: {
                        tabId: tabData.id,
                    },
                    html: '<a class="crm-entity-section-tab-link" href="#">' + tabData.name + '</a>',
                }
            );

            // Добавим созданный пункт меню к остальным пунктам меню
            BX.append(
                tabMenuContainer,
                tabManager._menuContainer
            );

            // Если мы хотим подгружать контент вкладки динамически то опишем как надо это делать
            tabData.loader = {};

            // Адрес на который будет делаться запрос при первом показе вкладки
            tabData.loader.serviceUrl = '/router.php?id=' + params.entity_id + '&sessid=' + BX.bitrix_sessid();

            // Параметры которые будут отправлены в ajax запросе, параметры передаются в массиве PARAMS
            tabData.loader.componentData = {foo: 'bar'};

            // Контейнер в который будет вставлен ответ сервера
            tabData.loader.container = tabContainer;

            // Идентификатор вкладки, так же попадет в массив PARAMS
            tabData.loader.tabId = tabData.id;

            // Добавим новую вкладку в менеджер вкладок
            tabManager._items.push(
                BX.Crm.EntityDetailTab.create(
                    tabData.id,
                    {
                        manager: tabManager,
                        data: tabData,
                        container: tabContainer,
                        menuContainer: tabMenuContainer,
                    }
                )
            );
        },
        function () {
            // Если не удалось найти менеджер вкладок можно вывести уведомление
        }
    );
}
