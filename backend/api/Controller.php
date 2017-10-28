<?php
namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use  \Psr\Container\ContainerInterface;
/**
 * Class Controller
 */
abstract class  Controller
{
    protected $ci;
    protected $app;
    protected $pdo;
    protected $db;
    protected $redis;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
        $this->app = $this->ci->get('app');
        $this->db = $this->ci->get('db');
        $this->pdo = $this->ci->get('pdo');
        $this->redis = $this->ci->get('redis');
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
     * current cookie[_front]
     * @param Request $request
     * @return string
     */
    public function cookieId(Request $request) {
        $current_cookie = $request->getCookieParams();
        if (isset($current_cookie['_backend']) && strlen($current_cookie['_backend']) == 32) {
            $cookie_id = $current_cookie['_backend'];
        } else {
            $cookie_id = md5(uniqid() . uniqid());
        }
        return $cookie_id;
    }

    /**
     * 获取用户登录信息
     * @param $key
     * @param Request $request
     * @return bool
     */
    public function getLogin($key,Request $request)
    {
        $cookie_id = $this->cookieId($request);
        $login_id = 'ys_backend_' . $cookie_id;
        $res = $this->redis->get($login_id);
        $data = json_decode($res, true);
        if ($data) {
            return isset($data[$key]) ? $data[$key] : false;
        } else {
            return false;
        }
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
        $login_id = 'ys_backend_' . $cookie_id;
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
     * 当前用户信息
     * @param $key
     * @param Request $request
     * @return bool
     */
    public function currentInfo($key,Request $request)
    {
        $cache_info = $this->getLogin($key, $request);
        $id = $cache_info['id'];
        $user_info = $this->db->admin()->where(['id' => $id, 'status'=>1])->fetch();
        $return = false;
        if ($user_info) {
            $return = iterator_to_array($user_info->getIterator());
        }
        return $return;
    }

    /**
     * logout
     * @param Request $request
     */
    public function setLogout(Request $request)
    {
        $cookie_id = $this->cookieId($request);
        $login_id = 'ys_backend_' . $cookie_id;
        return $this->redis->del($login_id);
    }

    public function resetCookie() {
        $key = md5(uniqid() . uniqid());
        setcookie('_backend', $key, time() + 3600 * 24 * 365, '/');
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
        }),['status'=>[1,2]]);
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