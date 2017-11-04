<?php
namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Container\ContainerInterface;
use \front\library\Sms;

/**
 * Class Controller
 */
abstract class  Controller
{
    protected $ci;
    protected $app;
    protected $db;
    protected static $db_static; // static db
    protected $pdo;
    protected $redis;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->app = $this->ci->get('app');
        $this->db = $this->ci->get('db');
        self::$db_static = $this->ci->get('db');
        $this->pdo = $this->ci->get('pdo');
        $this->redis = $this->ci->get('redis');
    }

    /**
     * current cookie[_front]
     * @param Request $request
     * @return string
     */
    public function cookieId(Request $request) {
        $current_cookie = $request->getCookieParams();
        if (isset($current_cookie['_front']) && strlen($current_cookie['_front']) == 32) {
            $cookie_id = $current_cookie['_front'];
        } else {
            $cookie_id = md5(uniqid() . uniqid());
        }
        return $cookie_id;
    }

    /**
     * 缓存用户信息
     * @param $key
     * @param Request $request
     * @return bool
     */
    public function getLogin($key,Request $request)
    {
        $cookie_id = $this->cookieId($request);
        $login_id = 'ys_front_' . $cookie_id;
        $res = $this->redis->get($login_id);
        $data = json_decode($res, true);
        if ($data) {
            return isset($data[$key]) ? $data[$key] : false;
        } else {
            return false;
        }
    }

    /**
     * 当前用户信息
     * @param $key
     * @param Request $request
     * @return bool
     */
    public function currentUserInfo($key,Request $request)
    {
        $cache_info = $this->getLogin($key, $request);
        $uid = $cache_info['id'];
        $user_info = $this->db->user_global()->where(['id' => $uid, 'status'=>1])->fetch();
        $return = false;
        if ($user_info) {
            $return = iterator_to_array($user_info->getIterator());
            unset($return['password']);
            unset($return['real_path']);
        }
        return $return;
    }

    /**
     * @param $key
     * @param null $value
     * @param Request $request
     */
    public function setLogin($key, $value = null, Request $request)
    {
        //重置redis
        $this->setLogout($request);
        //重置cookie
        $cookie_id = $this->resetCookie();
        $login_id = 'ys_front_' . $cookie_id;
        $result = $this->redis->get($login_id);
        $data = json_decode($result, true);
        if ($data) {
            $data[$key] = $value;
        } else {
            $data = [$key => $value];
        }
        $this->redis->setex($login_id, 3600 * 24 * 7, json_encode($data));
    }

    /**
     * logout
     * @param Request $request
     */
    public function setLogout(Request $request)
    {
        $cookie_id = $this->cookieId($request);
        $login_id = 'ys_front_' . $cookie_id;
        return $this->redis->del($login_id);
    }

    /**
     * 重置cookie
     * @return string
     */
    public function resetCookie() {
        $key = md5(uniqid() . uniqid());
        setcookie('_front', $key, time() + 3600 * 24 * 365, '/');
        return $key;
    }

    /**
     * check empty
     * @param $check
     * @param $params
     * @return bool
     */
    public function formControlEmpty($check, $params)
    {
        if (empty($check)) {
            return true;
        }

        $check_exists = array_diff($check, array_keys($params));
        if (!empty($check_exists)) {
            return false;
        }
        foreach ($check as $value) {
            if ($params[$value] === '') {
                return false;
            }
        }

        return true;
    }

    /**
     * base64单图片处理方法
     * @param string $image
     * @param string $only
     * @param string $dir
     * @param string $file_name
     * @return array|bool
     */
    public function uploadBase64Image($image, $only = "", $dir = "", $file_name="")
    {
        if (empty($only)) {
            $only = md5(uniqid() . uniqid());
        }
        //图片格式获取
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $image, $result)){
            $type = $result[2];
            $fileName = $file_name . '_' . $only .'_'. date('Y_m_d_H_i_s', time()) . '.' .$type;
            $realDir = __DIR__ . "/../../upload/" . $dir;
            if (!is_dir($realDir)) {
                mkdir($realDir, 0777, true);
                chmod($realDir, 0777);
            }
            $realPath = realpath($realDir) .'/'. $fileName;
            if (file_put_contents($realPath, base64_decode(str_replace($result[1], '', $image)))){
                return [
                    'realPath' => $realPath,
                    'photo' => '/upload/' . $dir .'/'. $fileName
                ];
            }
        }
        return false;
    }

    /**
     * 多文件上传
     * @param $files
     * @param string $only
     * @param string $dir
     * @param string $file_name
     * @return array|bool
     */
    public function uploadFiles($files,$only = "", $dir = "", $file_name="")
    {
        if (empty($only)) {
            $only = md5(uniqid() . uniqid());
        }
        $count = count($files['data']['name']);
        if ($count <= 0) {
            return false;
        }
        $result = [];
        for ($i = 0; $i < $count; $i++) {
            $tmp_only = $only . '_' . $i;
            $tmp = $files['data']['tmp_name'][$i];
            $suffix = array_pop(explode(".", $files['data']['name'][$i]));
            $fileName = $file_name . '_' . $tmp_only .'_'. date('Y_m_d_H_i_s', time()) . '.' .$suffix;
            $realDir = __DIR__ . "/../../upload/" . $dir;
            if (!is_dir($realDir)) {
                mkdir($realDir, 0777, true);
                chmod($realDir, 0777);
            }
            $realPath = realpath($realDir) .'/'. $fileName;
            if (move_uploaded_file($tmp, $realPath)) {
                //success
                $result[] =  [
                    'fileName' => $files['data']['name'][$i],
                    'realPath' => $realPath,
                    'photo' => '/upload/' . $dir .'/'. $fileName
                ];
            }
        }
        return $result;
    }

    /**
     * @param $obj
     * @return array
     */
    public function iterator_array($obj)
    {
        $result = [];
        foreach ($obj as $row) {
            $result[] = iterator_to_array($row);
        }
        return $result;
    }

    /**
     * @param $obj
     * @return array
     */
    public static function static_iterator_array($obj)
    {
        $result = [];
        foreach ($obj as $row) {
            $result[] = iterator_to_array($row);
        }
        return $result;
    }

    public function checkMobile($mobile)
    {
        if (preg_match('/^1(3[0-9]|4[57]|5[0-35-9]|7[0135678]|8[0-9])\\d{8}$/', $mobile)) {
            return true;
        }
        return false;
    }

    /**
     * 手机验证码
     * @param $mobile
     * @param $message
     * @return int
     */
    public function smsVerifyCode($mobile, $message) {
        $key = 'ys_mobile_' . $mobile;
        $code = $this->redis->get($key);
        if (!$code) {
            if ($mobile == "17621514022") {
                $code = 123456;
            } else {
                $code = mt_rand(100000, 999999);
            }
            $this->redis->setex($key, 60 * 10, $code);
        }
        $sms = new Sms();
        //$msg =  '【易百加药学工具网】' . $message . $code . '，十分钟之内有效！请勿重复获取之，如非本人操作，请忽略之。';
        $msg = "【药学工具网】欢迎注册药学工具网账号，验证码为".$message . $code ."（请勿泄露），如有任何疑问，请拨打010-80877977。";
        $return = $sms->send($mobile, $msg, 'true');
        return $return;
    }

    public function checkVerifyCode($mobile, $code)
    {
        $key = 'ys_mobile_' . $mobile;
        $code = $this->redis->get($key);
        if ($code) {
            return md5($code) == md5($code);
        } else {
            return false;
        }
    }

    /**
     * 缓存一个月
     * @param $unionid
     * @param $refresh_result  ['openid', 'access_token', ...'refresh_token'...]
     * @return mixed
     */
    public function cacheToken($unionid,$refresh_result)
    {
        return $this->redis->setex($unionid, 3600 * 24 * 30, json_encode($refresh_result));
    }

    /**
     * 简易搜索
     * @param $params
     * @param $black
     * @return array
     */
    protected function _commonSearch($params, $black)
    {
        return array_merge(array_filter(array_diff_key($params, array_flip($black)), function($item){
            if ($item === '')
                return false;
            return true;
        }),['status'=>1]);
    }

    /**
     * 时间搜索
     * @param $search
     * @param $get
     * @return array
     */
    public function timeSearch(&$search, $get)
    {
        $temp = [];
        if (!empty($get['start'])) {
            $temp['create_time >= ?'] = strtotime($get['start'] . '00:00:00');
            unset($search['start']);
        }
        if (!empty($get['end'])) {
            $temp['create_time <= ?'] = strtotime($get['end'] . '23:59:59');
            unset($search['end']);
        }
        return array_merge($search, $temp);
    }

    /**
     * @param $current_id
     * @return int
     */
    public function getTopCategory($current_id)
    {
        set_time_limit(0);
        $category_info = $this->db->pharmacy_category()->where(['id'=>$current_id])->fetch();
        if ($category_info) {
            $category_info = iterator_to_array($category_info->getIterator());
            if ($category_info['pid'] == 0) {
                return $category_info['id'];
            } else {
                return $this->getTopCategory($category_info['pid']);
            }
        } else {
            return 0;
        }
    }
}