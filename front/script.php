<?php

/**
 *再书脚本
 */
class againBook{

    public function againsend(){
        $url   = "www.twicebook.top/account/registe";
        $str   = "来再书舒宁电器计深圳科技划深圳环时间科技舒宁电器发改委王者荣耀阿萨德飞今年可能都十分难受的是快点快点看呃呃我发哦臀大这种明年年初你的请问如同和国内出现的公婆撒啊基地散发的书法大赛吃呀看的豆腐花苏里科夫的绝哦我么么哒哦求欧盟租房安顿的";
        $name  = $this->getH($str);
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
        // 利用preg_split函数，将汉字字符串拆分成数组，第一个参数是正则匹配，必须加上u，因为是utf8编码
        // 这里不能使用substr或者mb_substr等，因为这些方法是针对字符有效的，汉字占2或者3个字符
        $str = preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);

        // 利用shuffle函数，打乱汉字数组
        // 不能使用str_shuffle函数，因为那个是打乱字符的
        shuffle($str);

        // 从数组中截取前4个元素，得到的就是一个汉字数组
        $str = array_slice($str, 0, 4);

        // implode：将数组拼凑成字符串
        $str = implode('', $str);
        return $str;
    }

}

$m = new againBook();
for($i = 0; $i < 35; $i++){
    $m->againsend();
}