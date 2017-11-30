<?php

/**
 *再书脚本
 */
class againBook{

    public function againsend(){
        $url   = "www.twicebook.top/account/registe";
        $name  = array(
            "abc" . time() . "qq",
            "德华" . date('w'),
            "小赖" . date('w'),
            "王者荣耀" . date('w'),
            "王者荣耀1" . date('w'),
            "王者荣2" . date('w'),
            "王者3" . date('w'),
            "安抚" . date('w'),
            "阿斯蒂芬" . date('w'),
            "书记" . date('w'),
            "发改委" . date('w'),
            "再书" . date('w'),
            "舒宁电器" . date('w'),
            "二手" . date('w'),
            "深圳环时间科技" . date('w'),
            "深圳科技" . date('w'),
            date('w') . "哥哥妹妹",
            date('w') . "小小",
            date('w') . "哥哥asdf7妹妹",
        );
        $key   = array_rand($name);
        $name  = $name[$key];
        $email = "abc" . time() . "@qq.com";
        $pass  = md5('abc');
        $data  = array(
            'name'     => $name,
            'password' => $pass,
            'email'    => $email
        );
        self::curl_post_contents($url, $data, 100);
    }

    /**
     * curl Post数据
     */
    static function curl_post_contents($url, $data = array(), $timeout = 10){
        //$userAgent = 'xx5.com PHP5 (curl) ' . phpversion();
        $referer = $url;
        if(!is_array($data) || !$url){
            return '';
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);            //设置超时
        //curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER, $referer);      //设置 referer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);      //跟踪301
        curl_setopt($ch, CURLOPT_POST, 1);             //指定post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      //添加变量
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //返回结果
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

}

$m = new againBook();
$m->againsend();