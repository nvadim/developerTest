<?php
namespace Nvadim;

use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;
use Bitrix\Iblock\ElementTable;

class CIblockElementDev
{
    /**
     * Кешированный метод получения списка элементов
     *
     * @param $params available keys:
     * 		"select" => array of fields in the SELECT part of the query, aliases are possible in the form of "alias"=>"field"
     * 		"filter" => array of filters in the WHERE part of the query in the form of "(condition)field"=>"value"
     * 		"group" => array of fields in the GROUP BY part of the query
     * 		"order" => array of fields in the ORDER BY part of the query in the form of "field"=>"asc|desc"
     * 		"limit" => integer indicating maximum number of rows in the selection (like LIMIT n in MySql)
     * 		"offset" => integer indicating first row number in the selection (like LIMIT n, 100 in MySql)
     *		"runtime" => array of entity fields created dynamically
     *
     * @param $cacheParams available keys:
     * 		"ttl" => время кеша
     *      "initDir" => папка кеша
     *
     * @return array|bool
     */
    public static function getList($params, $cacheParams)
    {
        if (!is_array($params['order']) || empty($params['order']))
            $params['order'] = ['ID' => 'ASC'];

        if (!is_array($params['filter']) || empty($params['filter']))
            return false;

        $result = [];

        if ($cacheParams['ttl'])
            $life_time = $cacheParams['ttl'];
        else
            $life_time = 86400;

        $cache_id = "iblock_elements::" . json_encode(array_merge($params, $cacheParams));

        $obCache = Cache::createInstance();
        $initDir = false;
        if($cacheParams['initDir']) {
            $initDir = $cacheParams['initDir'];
        }
        if ($obCache->initCache($life_time, $cache_id, $initDir)) {
            $result = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            Loader::includeModule("iblock");

            $elementList = ElementTable::getList($params);
            while($el = $elementList->fetch()) {
                $result[] = $el;
            }

            $obCache->endDataCache($result);
        }

        return $result;
    }
}
