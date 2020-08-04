<?php
namespace Dmitry\Edu7;

use Bitrix\Main\Entity;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;

class CityTable extends DataManager
{
    public static function getTableName()
    {
        return 'my_city';
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
            new Entity\IntegerField('REGION_ID'),
            (new Reference(
                'REGION',
                RegionTable::class,
                Join::on('this.REGION_ID', 'ref.ID')
            ))
                ->configureJoinType('inner')
        ];
    }
}

