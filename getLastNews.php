<?php

$COUNT_LAST_ITEMS = 5; // кол-во выводимых новостей

// отладчик d
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

class Tools
{
    public static function request($url)
    {
        $ch = curl_init();
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url
        ];

        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}


$url = 'https://lenta.ru/rss';

$data = Tools::request($url);

//parsed xml data
$xml_data = simplexml_load_string($data);

$result = [];
for ($i = 0; $i < $COUNT_LAST_ITEMS; $i++) {
    $item = $xml_data->channel->item[$i];
    $result[] = [
        'name' => (string)$item->title,
        'link' => (string)$item->link,
        'anons' => (string)$item->description
    ];
}
unset($item);

foreach ($result as $item) {
    echo "name: {$item['name']}, link: {$item['link']}, anons: {$item['anons']}" . PHP_EOL;
}
