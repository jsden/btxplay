<?php
require_once __DIR__ . "/../../modules/dmitry.edu7/lib/region.php";
require_once __DIR__ . "/../../modules/dmitry.edu7/lib/city.php";

class CCityElement extends CUserTypeString
{
    const USER_TYPE_ID = 'city';

    function GetUserTypeDescription()
    {
        parent::GetUserTypeDescription();

        return array_merge(
            parent::GetUserTypeDescription(),
            [
                "USER_TYPE_ID" => static::USER_TYPE_ID,
                // "CLASS_NAME" => __CLASS__,
                "DESCRIPTION" => GetMessage("CUSTOM_CITY"),
                "EDIT_CALLBACK" => array(__CLASS__, 'GetPublicEdit'),
                "VIEW_CALLBACK" => array(__CLASS__, 'GetPublicView'),

                //Можно задать компонент для отображения значений свойства в публичной части.
                //"VIEW_COMPONENT_NAME" => "my:system.field.view",
                //"VIEW_COMPONENT_TEMPLATE" => "string",
                //и для редактирования
                //"EDIT_COMPONENT_NAME" => "my:system.field.view",
                //"EDIT_COMPONENT_TEMPLATE" => "string",
                // также можно задать callback для отображения значений
                // "VIEW_CALLBACK" => callable
                // и для редактирования
                // "EDIT_CALLBACK" => callable
            ]
        );
    }

    function GetEditFormHTML($arUserField, $arHtmlControl)
    {
        return '<input type="button" value="Click me dude">';
    }

    function GetAdminListEditHTML($arUserField, $arHtmlControl)
    {
        return '<input type="button" value="Click me dude">';
    }

    /**
     * Array (
     *  [ID] => 86
     *  [ENTITY_ID] => CRM_DEAL
     *  [FIELD_NAME] => UF_CRM_CITY
     *  [USER_TYPE_ID] => city
     *  [XML_ID] =>
     *  [SORT] => 100
     *  [MULTIPLE] => N
     *  [MANDATORY] => N
     *  [SHOW_FILTER] => N
     *  [SHOW_IN_LIST] => Y
     *  [EDIT_IN_LIST] => Y
     *  [IS_SEARCHABLE] => N
     *  [SETTINGS] => Array (
     *      [SIZE] => 20
     *      [ROWS] => 1
     *      [REGEXP] =>
     *      [MIN_LENGTH] => 0
     *      [MAX_LENGTH] => 0
     *      [DEFAULT_VALUE] =>
     *  )
     *  [EDIT_FORM_LABEL] => Город
     *  [LIST_COLUMN_LABEL] => Город
     *  [LIST_FILTER_LABEL] => Город
     *  [ERROR_MESSAGE] =>
     *  [HELP_MESSAGE] =>
     *  [USER_TYPE] => Array (
     *      [USER_TYPE_ID] => city
     *      [CLASS_NAME] => CUserTypeString
     *      [DESCRIPTION] => ВЫБОР ГОРОДА!!!
     *      [BASE_TYPE] => string
     *      [EDIT_CALLBACK] => Array (
     *          [0] => CCityElement
     *          [1] => GetPublicEdit
     *      )
     *      [VIEW_CALLBACK] => Array (
     *          [0] => CUserTypeString
     *          [1] => GetPublicView
     *      )
     *  )
     * [ENTITY_VALUE_ID] => 11 )
     *
     * $arAdditionalParameters
     * Array (
     *  [bVarsFromForm] =>
     *  [form_name] => UF_CRM_CITY
     *  [CONTEXT] => CRM_EDITOR
     * )
     *
     * @param array $arUserField
     * @param array $arAdditionalParameters
     * @return mixed
     */
    public static function GetPublicEdit($arUserField, $arAdditionalParameters = array())
    {
        list($regionId, $regionCity) = explode('.', $arUserField['VALUE']);

        return static::getRegionDropdown($regionId, $regionCity)
            . static::getCityDropdown($regionId, $regionCity)
            . static::getHidden($arUserField);
    }

    public static function GetPublicView($arUserField, $arAdditionalParameters = array())
    {
        $result = '';

        list($regionId, $regionCity) = explode('.', $arUserField['VALUE']);

        $region = \Dmitry\Edu7\RegionTable::getById($regionId);
        if ($region)
        {
            $regionData = $region->fetch();
            $result .= "<strong>{$regionData['NAME']}</strong>: ";

            $city = \Dmitry\Edu7\CityTable::getById($regionCity);
            if ($city)
            {
                $cityData = $city->fetch();
                $result .= $cityData['NAME'];
            }
        }

        return $result;
    }

    public static function getHidden($arUserField)
    {
        return '<input type="hidden" name="' . $arUserField['FIELD_NAME'] . '" value="">';
    }

    public static function getRegionDropdown($regionId, $cityId)
    {
        $result = '<select onChange="BX.Edu7.field.changeRegion(this);">';

        $optionsResult = \Dmitry\Edu7\RegionTable::getList([]);
        while ($option = $optionsResult->fetch())
        {
            $optionHTML = '<option value="' . intval($option['ID']) . '"';

            if ($regionId == $option['ID'])
            {
                $optionHTML .= ' selected';
            }

            $optionHTML .= '>' . htmlspecialchars($option['NAME']). '</option>';

            $result .= $optionHTML;
        }


        $result .= '</select>';

        return $result;
    }

    public static function getCityDropdown($regionId, $cityId)
    {
        $result = '<select onChange="BX.Edu7.field.changeCity(this);">';

        $optionsResult = \Dmitry\Edu7\CityTable::getList([]);
        while ($option = $optionsResult->fetch())
        {
            $optionHTML = '<option data-region-id="' . intval($option['REGION_ID']) . '" value="' . intval($option['ID']) . '"';

            if ($option['REGION_ID'] == $regionId)
            {
                if ($cityId == $option['ID'])
                {
                    $optionHTML .= 'selected';
                }
            }
            else
            {
                $optionHTML .= ' style="display: none;" ';
            }

            $optionHTML .= '>' . htmlspecialchars($option['NAME']). '</option>';

            $result .= $optionHTML;
        }


        $result .= '</select>';

        return $result;
    }
}

$manager = new \Itech\Bitrix\Event\Manager();
$manager->add(
        \Itech\Bitrix\Event\Manager::EVENT_ON_USER_TYPE_BUILD_LIST,
        ['CCityElement', 'GetUserTypeDescription']
);