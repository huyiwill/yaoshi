<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SiteController
 * @package backend\api
 */
class SiteController extends Controller
{
    public function actionDefault(Request $request, Response $response)
    {
        return $response->withRedirect('/index.html');
    }

    /**
     * login
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionLogin(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        $admin_info = $this->db->admin()->where(['account' => $post['account'], 'status' => 1])->fetch();
        if ($admin_info) {
            if (md5($post['password']) === $admin_info['password']) {
                $return = [
                    'status' => true,
                ];
                $admin_info = iterator_to_array($admin_info->getIterator());
                unset($admin_info['password']);
                //登录成功
                $this->setLogin('_ys_backend_login', $admin_info, $request);
            } else {
                $return = [
                    'status' => false,
                    'message' => "密码错误"
                ];
            }
        } else {
            $return = [
                'status' => false,
                'message' => "账户不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * 管理员信息获取
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionAccount(Request $request, Response $response)
    {
        $current_account_info = $this->currentInfo('_ys_backend_login', $request);
        $return = ['status'=>true, 'message'=>'账户信息不存在', 'data'=>[]];
        if ($current_account_info) {
            $return = ['status'=>true, 'message'=>'', 'data'=>$current_account_info];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * logout
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionLogout(Request $request, Response $response)
    {
        $return = ['status'=>false, 'message'=>'账号异常'];
        if($this->setLogout($request)) {
            $return = ['status'=>true, 'message'=>''];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}