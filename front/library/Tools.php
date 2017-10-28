<?php

namespace front\library;

use front\api\Controller;

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
     * 临时简单调试log
     * @param $filename
     * @param string $key
     * @param string $content
     * @return bool|int
     */
    public static function logger($filename, $key='', $content='')
    {
        //日志
        $temp_dir = sys_get_temp_dir();
        if (!is_dir($temp_dir)) {
            return 0;
        }
        $filename = $temp_dir.DIRECTORY_SEPARATOR.'log-'.$filename.'-'.date('Y-m-d', time()).'.log';
        $content = $key .'--->'. $content."\r\n \r\n \r\n";
        return file_put_contents($filename, $content, FILE_APPEND);
    }

    /**
     * @param $data
     * @param int $pid
     * @param int $is_user
     * @return array
     */
    public static function getTree($data, $pid=0,$is_user=0)
    {
        $return = [];
        if (!empty($data)) {
            foreach ($data as $key=>$item) {
                if ($is_user == 1) {

                }
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
                    $str .= "<option value='{$item['id']}'>" . $sign . $item['name'] . '</option>';
                    $str .= self::getOptions($data, $item['id'], $level);
                }
            }
        }
        return $str;
    }

    /* putrid code */
    public static function invitationCode($number)
    {
        $channel = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPOPQRSTUVWXYZ';
        $code = '';
        for ($i=0; $i<$number; $i++) {
            $code .= $channel[mt_rand(0,51)];
        }
        return $code;
    }
}