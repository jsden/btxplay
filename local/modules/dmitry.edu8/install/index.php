<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\Application;
use Bitrix\Main\IO\Directory;

Loc::loadMessages(__FILE__);

/**
 * Class dmitry_edu8
 *
 * Имя класса должно соответствовать папке модуля
 *
 */
class dmitry_edu8 extends CModule
{
    public function __construct()
    {

        if (file_exists(__DIR__ . "/version.php"))
        {

            $arModuleVersion = array();

            include_once(__DIR__ . "/version.php");

            $this->MODULE_ID = str_replace("_", ".", get_class($this));
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage("EDU8_MODULE_NAME");
            $this->MODULE_DESCRIPTION = Loc::getMessage("EDU8_MODULE_DESCRIPTION");
            $this->PARTNER_NAME = Loc::getMessage("EDU8_PARTNER_NAME");
            $this->PARTNER_URI = Loc::getMessage("EDU8_PARTNER_URI");
        }

        return false;
    }

    /**
     * @return bool
     */
    public function DoInstall()
    {
        /** @var CAllMain $APPLICATION */
        global $APPLICATION;

        if (CheckVersion(ModuleManager::getVersion("main"), "14.00.00"))
        {

            $this->InstallFiles();
            $this->InstallDB();

            ModuleManager::registerModule($this->MODULE_ID);

            $this->InstallEvents();
        } else
        {

            $APPLICATION->ThrowException(
                Loc::getMessage('EDU8_INSTALL_ERROR_VERSION')
            );
        }

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('EDU8_INSTALL_TITLE') . " \"" . Loc::getMessage("EDU8_MODULE_NAME") . "\"",
            __DIR__ . "/step.php"
        );

        return false;
    }

    public function InstallFiles()
    {
        /*
        CopyDirFiles(
            __DIR__ . "/assets/scripts",
            Application::getDocumentRoot() . "/bitrix/js/" . $this->MODULE_ID . "/",
            true,
            true
        );

        CopyDirFiles(
            __DIR__ . "/assets/styles",
            Application::getDocumentRoot() . "/bitrix/css/" . $this->MODULE_ID . "/",
            true,
            true
        );
        */

        return false;
    }

    public function InstallDB()
    {
        global $DB;

        $DB->RunSQLBatch(__DIR__ . "/dump.sql");

        $DB->Query("DROP TABLE IF EXISTS my_city");
        $DB->Query("CREATE TABLE `my_city` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `NAME` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            `REGION_ID` int(11) NOT NULL,
            PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $DB->Query("DROP TABLE IF EXISTS my_region");
        $DB->Query("CREATE TABLE `my_region` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `NAME` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`ID`)
            ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        return false;
    }

    public function InstallEvents()
    {

        EventManager::getInstance()->registerEventHandler(
            "main",
            "OnBeforeEndBufferContent",
            $this->MODULE_ID,
            "Dmitry\Edu8\Main",
            "appendScriptsToPage"
        );

        EventManager::getInstance()->registerEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            'Dmitry\Edu8\CCityElement',
            'GetUserTypeDescription'
        );

        return false;
    }

    public function DoUninstall()
    {

        global $APPLICATION;

        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);

        $APPLICATION->IncludeAdminFile(
            Loc::getMessage('EDU8_UNINSTALL_TITLE') . '"' . Loc::getMessage('EDU8_MODULE_NAME') . '"',
            __DIR__ . "/unstep.php"
        );

        return false;
    }

    public function UnInstallFiles()
    {
        /*
        Directory::deleteDirectory(
            Application::getDocumentRoot() . "/bitrix/js/" . $this->MODULE_ID
        );

        Directory::deleteDirectory(
            Application::getDocumentRoot() . "/bitrix/css/" . $this->MODULE_ID
        );
        */

        return false;
    }

    public function UnInstallDB()
    {
        global $DB;

        $DB->Query("DROP TABLE my_city");
        $DB->Query("DROP TABLE my_region");

        Option::delete($this->MODULE_ID);

        return false;
    }

    public function UnInstallEvents(){

        EventManager::getInstance()->unRegisterEventHandler(
            "main",
            "OnBeforeEndBufferContent",
            $this->MODULE_ID,
            "Dmitry\Edu8\Main",
            "appendScriptsToPage"
        );

        EventManager::getInstance()->unRegisterEventHandler(
            'main',
            'OnUserTypeBuildList',
            $this->MODULE_ID,
            'Dmitry\Edu8\CCityElement',
            'GetUserTypeDescription'
        );

        return false;
    }
}

