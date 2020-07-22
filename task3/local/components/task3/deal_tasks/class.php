<?php
use Bitrix\Main\UI\Filter\Options;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

class CDealTasks extends CBitrixComponent
{
    public function getTasks($id, Options $filterOptions, $filter)
    {
        $tasks = $this->getAllTasks($id);

        $filterFields = $filterOptions->getFilter($filter);

        return array_filter(
            $tasks,
            function($item) use ($filterFields)
            {
                return $this->isMatch($item, $filterFields);
            }
        );
    }

    protected function getAllTasks($id)
    {
        $dealRepo = new \Itech\Bitrix\Repo\CRM\DealRepo();
        $deal = $dealRepo->getById($id);
        $activity = $deal->getActivity();

        return array_map(
            function ($item)
            {
                return [
                    'data' => [
                        'TYPE_NAME' => $item->TYPE_NAME,
                        'DEADLINE'  => $item->DEADLINE,
                    ]
                ];
            },
            $activity
        );
    }

    protected function isMatch($item, $filterFields)
    {

        foreach ($filterFields as $fieldName => $fieldValue)
        {
            switch ($fieldName)
            {
                case 'TYPE_NAME':
                    return strncasecmp($item['data']['TYPE_NAME'], $fieldValue, 100) === 0;

                default:
                    return true;
            }
        }
    }
}
