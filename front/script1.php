<?php

/**
 *再书脚本
 */
class againBook{

    public function againsend(){
        $url   = "www.twicebook.top/account/registe";
        $str   = "来再书舒宁电器计天天酷派王者荣耀华为手机总部苹果x赖基华深圳科技划深圳环时间科技舒宁电器发改委王者荣耀阿萨德飞今年可能都十分难受的是快点快点看呃呃我发哦臀大这种明年年初你的请问如同和国内出现的公婆撒啊基地散发的书法大赛吃呀看的豆腐花苏里科夫的绝哦我么么哒哦求欧盟租房安顿的";
        $name  = $this->getH($str);
        $email = substr(md5(uniqid()), 1, 8) . substr(md5(date('His')), 1, 5) . "@163.com";
        $pass  = md5('abc');
        $data  = array(
            'name'     => $name,
            'password' => $pass,
            'email'    => $email
        );
        $res   = self::curl_post_contents($url, $data, 100);
        echo $res;
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

    public function createRandomStr($length){
        $str    = "来计划laijihua阿萨德飞今年可能都十分难受的是快点快点看呃呃我发哦臀大这种明年年初你的请问如同和国内出现的公婆撒啊基地散发的书法大赛吃呀看的豆腐花苏里科夫的绝哦我么么哒哦求欧盟租房安顿的";
        $strlen = strlen($str);
        $len    = &$strlen;
        while($length > $strlen){
            $str .= $str;
            $strlen += $len;
        }
        $str = str_shuffle($str);
        return substr($str, 0, $length);
    }

    public function getH($str){
        $str = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
        shuffle($str);
        $rand = rand(1, 11);
        $str  = array_slice($str, 0, $rand);
        $str  = implode('', $str);
        return $str;
    }

}

$m = new againBook();
//$m->againsend();
for($i = 0; $i < 5; $i++){
    $m->againsend();
}