<?php
require_once "./bootstrap.php";

class CreateLeads extends DmitryTestBase
{
    public function __invoke()
    {
        $arContacts = $this->getContacts();

        while ($arContact = $arContacts->Fetch())
        {
            if ($leadId = $this->createLead($arContact))
            {
                $this->createTimeLine($arContact, $leadId);
            }
        }
    }

    /**
     * Создать запись в таймлайне
     *
     * @param $arContact
     * @param $leadId
     * @return array|int
     */
    protected function createTimeLine($arContact, $leadId)
    {
        $link = $this->getContactHyperLink($arContact);

        $arStatus = [
            'AUTHOR_ID' => self::CREATOR_ID,
            'TEXT'      => $this->fromUtf("Создан на основании контакта {$link}"),
            'BINDINGS'  => [
                [
                    'ENTITY_ID'      => $arContact['ID'],
                    'ENTITY_TYPE_ID' => CCrmOwnerType::Contact,
                ],
                [
                    'ENTITY_ID'      => $leadId,
                    'ENTITY_TYPE_ID' => CCrmOwnerType::Lead,
                ],
            ]
        ];

        return \Bitrix\Crm\Timeline\CommentEntry::create(
            $arStatus
        );
    }

    /**
     * Создать лид на основании контакта
     *
     * @param $arContact
     * @return bool|int
     */
    public function createLead($arContact)
    {
        $lead = new CCrmLead(false);

        $arFields = [
            'ASSIGNED_BY_ID'      => self::CREATOR_ID,
            'SOURCE_ID'           => 'PARTNER',
            'TITLE'               => $this->fromUtf("Лид для продвижения вебинара {$arContact['FULL_NAME']}"),
            'NAME'                => $arContact['NAME'],
            'LAST_NAME'           => $arContact['LAST_NAME'],
            'SECOND_NAME'         => $arContact['SECOND_NAME'],
            self::CREATION_METHOD => self::CREATION_METHOD_SCRIPT,
            'FM'                  => [
                'EMAIL' => [
                    'n0' => [
                        'VALUE'      => $this->fetchMulti($arContact['ID'], self::FIELD_EMAIL),
                        'VALUE_TYPE' => 'WORK',
                    ],
                ],
                'PHONE' => [
                    'n0' => [
                        'VALUE'      => $this->fetchMulti($arContact['ID'], self::FIELD_PHONE),
                        'VALUE_TYPE' => 'WORK'
                    ]
                ],
            ],
        ];

        if ($leadId = $lead->Add($arFields, true, ['DISABLE_USER_FIELD_CHECK' => true]))
        {
            return $leadId;
        } else
        {
            $this->error($lead->LAST_ERROR);

            return false;
        }
    }
}

(new CreateLeads)();

require_once "./shutdown.php";

