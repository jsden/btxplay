<?php
defined('B_PROLOG_INCLUDED') || die();

class CBPEdu9Activity extends CBPActivity
{
    public function __construct($name)
    {
        parent::__construct($name);
        $this->arProperties = array(
            "Title" => "",
            "DocumentType" => null,
            "Fields" => null,

            //return properties
            'ElementId' => null,
        );

        //return properties mapping
        $this->SetPropertiesTypes(array(
            'ElementId' => array(
                'Type' => 'int',
            ),
        ));
    }

    public function Execute()
    {
        $documentType = $this->DocumentType;
        $fields = $this->Fields;
        $fields['IBLOCK_ID'] = substr($documentType[2], 7); // strlen('iblock_') == 7

        if (!isset($fields["CREATED_BY"]))
        {
            $stateInfo = CBPStateService::getWorkflowStateInfo($this->getWorkflowInstanceId());
            if (intval($stateInfo["STARTED_BY"]) > 0)
            {
                $fields["CREATED_BY"] = $stateInfo["STARTED_BY"];
            }
        }

        $documentService = $this->workflow->GetService("DocumentService");
        $this->ElementId = $documentService->CreateDocument($documentType, $fields);

        return CBPActivityExecutionStatus::Closed;
    }

    public static function GetPropertiesDialog($paramDocumentType, $activityName, $arWorkflowTemplate, $arWorkflowParameters, $arWorkflowVariables, $arCurrentValues = null, $formName = "", $popupWindow = null)
    {
        if (!CModule::IncludeModule('lists'))
            return null;

        $documentType = null;
        if (!empty($arCurrentValues['lists_document_type']))
        {
            $documentType = explode('@', $arCurrentValues['lists_document_type']);
        }

        $runtime = CBPRuntime::GetRuntime();
        $documentService = $runtime->GetService("DocumentService");

        if (!is_array($arCurrentValues))
        {
            $arCurrentValues = array();

            $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
            if (!empty($arCurrentActivity["Properties"]['Fields']) && is_array($arCurrentActivity["Properties"]["Fields"]))
            {
                foreach ($arCurrentActivity["Properties"]["Fields"] as $k => $v)
                {
                    $arCurrentValues[$k] = $v;
                }
            }
            if (!empty($arCurrentActivity["Properties"]['DocumentType']))
            {
                $documentType = $arCurrentActivity["Properties"]['DocumentType'];
                $arCurrentValues['lists_document_type'] = implode('@', $documentType);
            }
        }
        elseif ($documentType)
        {
            $fields = $documentService->GetDocumentFields($documentType);
            foreach ($fields as $key => $value)
            {
                if (!$value["Editable"])
                    continue;

                $arErrors = array();
                $arCurrentValues[$key] = $documentService->GetFieldInputValue($documentType, $value, $key, $arCurrentValues, $arErrors);
            }
        }

        $dialog = new \Bitrix\Bizproc\Activity\PropertiesDialog(__FILE__, array(
            'documentType' => $paramDocumentType,
            'activityName' => $activityName,
            'workflowTemplate' => $arWorkflowTemplate,
            'workflowParameters' => $arWorkflowParameters,
            'workflowVariables' => $arWorkflowVariables,
            'currentValues' => $arCurrentValues,
            'formName' => $formName
        ));

        $dialog->setMap([
            'DocumentType' => self::getDocumentTypeField()
        ]);

        $dialog->setRuntimeData(array(
            "documentFields" => $documentType ? self::getDocumentFields($documentType) : [],
            "documentService" => $documentService,
            'listsDocumentType' => $documentType,
        ));

        return $dialog;
    }

    private static function getVisibleFieldsList($iblockId)
    {
        $list = new CList($iblockId);
        $listFields = $list->getFields();
        $result = array();
        foreach ($listFields as $key => $field)
        {
            if (strpos($key, 'PROPERTY_') === 0)
            {
                if (!empty($field['CODE']))
                    $key = 'PROPERTY_'.$field['CODE'];
            }
            $result[] = $key;
        }
        return $result;
    }

    private static function getDocumentFields(array $documentType)
    {
        $documentService = CBPRuntime::GetRuntime(true)->GetService("DocumentService");
        $fields = $documentService->GetDocumentFields($documentType);

        $listFields = static::getVisibleFieldsList(substr($documentType[2], 7));

        foreach ($fields as $fieldKey => $fieldValue)
        {
            if (
                $fieldKey !== "CREATED_BY"
                &&
                (!$fieldValue["Editable"] || $fieldKey == 'IBLOCK_ID' || !in_array($fieldKey, $listFields))
            )
            {
                unset($fields[$fieldKey]);
            }
        }

        return $fields;
    }

    private static function getDocumentTypeField()
    {
        $field = [
            'Name' => GetMessage('EDU9_DOC_TYPE'),
            'FieldName' => 'lists_document_type',
            'Type' => 'select',
            'Required' => true,
        ];

        $options = [];

        $iterator = CIBlock::GetList(array('SORT'=>'ASC', 'NAME' => 'ASC'), array(
            'ACTIVE' => 'Y',
            // 'TYPE' => array_keys($groups),
            'CHECK_PERMISSIONS' => 'N',
        ));

        while ($row = $iterator->fetch())
        {
            $value = 'lists@BizprocDocument@iblock_'.$row['ID'];
            $name = '['.$row['LID'].'] '.$row['NAME'];

            $options[$value] = $name;
        }

        $field['Options'] = $options;
        $field['Groups'] = []; // WTF???

        return $field;
    }

    public static function GetPropertiesDialogValues($documentType, $activityName, &$arWorkflowTemplate, &$arWorkflowParameters, &$arWorkflowVariables, $arCurrentValues, &$errors)
    {
        $errors = array();

        $runtime = CBPRuntime::GetRuntime();

        $documentType = null;
        if (!empty($arCurrentValues['lists_document_type']))
        {
            $documentType = explode('@', $arCurrentValues['lists_document_type']);
        }

        $arProperties = array("Fields" => array(), 'DocumentType' => $documentType);

        $documentService = $runtime->GetService("DocumentService");
        $arDocumentFields = $documentType ? $documentService->GetDocumentFields($documentType) : array();

        $iblockId = $documentType? substr($documentType[2], 7) : null;
        $listFields = $iblockId? static::getVisibleFieldsList($iblockId) : array();

        foreach ($arDocumentFields as $fieldKey => $fieldValue)
        {
            if ($fieldKey == 'IBLOCK_ID')
            {
                $arProperties["Fields"][$fieldKey] = $iblockId;
                continue;
            }

            if ($fieldKey !== "CREATED_BY")
            {
                if (!$fieldValue["Editable"] || !in_array($fieldKey, $listFields))
                    continue;
            }


            $arFieldErrors = array();
            $r = $documentService->GetFieldInputValue($documentType, $fieldValue, $fieldKey, $arCurrentValues, $arFieldErrors);

            if(is_array($arFieldErrors) && !empty($arFieldErrors))
            {
                $errors = array_merge($errors, $arFieldErrors);
            }

            if ($fieldValue["BaseType"] == "user")
            {
                if ($r === "author")
                {
                    //HACK: We can't resolve author for new document - setup target user as author.
                    $r = "{=Template:TargetUser}";
                }
                elseif (is_array($r))
                {
                    $qty = count($r);
                    if ($qty == 0)
                    {
                        $r = null;
                    }
                    elseif ($qty == 1)
                    {
                        $r = $r[0];
                    }
                }
            }

            if ($fieldValue["Required"] && ($r == null))
            {
                $errors[] = array(
                    "code" => "emptyRequiredField",
                    "message" => str_replace("#FIELD#", $fieldValue["Name"], GetMessage("BPCLDA_FIELD_REQUIED")),
                );
            }

            if ($r != null)
                $arProperties["Fields"][$fieldKey] = $r;
        }

        if (count($errors) > 0)
        {
            return false;
        }

        $arCurrentActivity = &CBPWorkflowTemplateLoader::FindActivityByName($arWorkflowTemplate, $activityName);
        $arCurrentActivity["Properties"] = $arProperties;

        return true;
    }
}