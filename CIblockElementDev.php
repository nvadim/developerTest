<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Data\Cache;

function d($var, $title = '')
{
        print '<pre style="width:9000;background: #000; color: #0f0; border: 1px solid #0f0;">';
        if ($title != '') {
            $ret
                = '<p style="margin-top:-0px;background-color:#DD0000;font-size:17px;padding:5px 5px 5px 5px; border:1px solid green;color:black;font-weight:bold">'
                . $title . '</p>';
        }
        $trace = debug_backtrace();
        $ret .= $trace[0]['file'] . ':' . $trace[0]['line'] . '</br></br>';

        $ret .= print_r($var, true);
        $ret = str_replace('=>', '<font color="#ffffff">=></font>', $ret);
        $ret = str_replace('[', '[ <font color="#FFFF00">', $ret);
        $ret = str_replace(']', '</font> ]', $ret);

        print $ret;
        print '</pre>';
        print "<hr>";
}


class CIblockElementDev
{
    /**
     * Кешированный метод получения списка элементов
     *
     * @param $sort
     * @param $filter
     * @param bool $groupBy
     * @param bool $navParams
     * @param bool $fields
     * @param bool $cacheTime
     * @return array|bool
     */
    public static function getList($sort, $filter, $groupBy = false, $navParams = false,  $fields = false, $cacheTime = false)
    {
        if (!is_array($sort) || empty($sort))
            $sort = ['ID' => 'ASC'];

        if (!is_array($filter) || empty($filter))
            return false;

        $result = [];

        if ($cacheTime)
            $life_time = $cacheTime;
        else
            $life_time = 86400;

        $cache_id = "iblock_elements::" . json_encode([$sort, $filter, $fields, $cacheTime]);

        $obCache = Cache::createInstance();
        if ($obCache->initCache($life_time, $cache_id, $sCachePath)) {
            $result = $obCache->getVars();
        } elseif ($obCache->startDataCache()) {
            Loader::includeModule("iblock");

            $rs = \CIBlockElement::GetList($sort, $filter, $groupBy, $navParams, $fields);
            while ($el = $rs->Fetch()) {
                $result[] = $el;
            }

            $obCache->endDataCache($result);
        }

        return $result;
    }
}
