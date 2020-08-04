<?php
namespace Dmitry\Edu8;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\Relations\OneToMany;

class RegionTable extends DataManager
{
    public static function getTableName()
    {
        return 'my_region';
    }

    public static function getMap()
    {
        return [
            new Entity\IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                ]
            ),
            new Entity\StringField('NAME'),
            (new OneToMany('BOOKS', CityTable::class, 'REGION'))
                ->configureJoinType('inner'),
        ];
    }
}

