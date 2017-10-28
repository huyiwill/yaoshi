<?php

namespace backend\library;

class Tools
{

    /**
     * 用curl post请求数据
     * @param $url
     * @param $data
     * @return mixed
     */
    public static function curlPost($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * 用curl get请求数据
     * @param $url
     * @return mixed
     */
    public static function curlGET($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,30);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * @param $data
     * @param int $pid
     * @return array
     */
    public static function getTree($data, $pid=0)
    {
        $return = [];
        if (!empty($data)) {
            foreach ($data as $key=>$item) {
                if ($item['pid'] == $pid) {
                    $item['item'] = self::getTree($data, $item['id']);
                    $return[] = $item;
                }
            }
        }
        return $return;
    }

    public static function getOptions($data, $pid=0, $level=0)
    {
        $str = "";
        if (!empty($data)) {
            $sign = str_repeat("-",$level);
            $level++;
            foreach ($data as $key=>$item) {
                if ($item['pid'] == $pid) {
                    $str .= "<option value='{$item['id']}' data-name='{$item['name']}'>" . $sign . $item['name'] . '</option>';
                    $str .= self::getOptions($data, $item['id'], $level);
                }
            }
        }
        return $str;
    }
}