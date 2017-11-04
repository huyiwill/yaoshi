<?php

namespace front\api;

use front\library\Tools;
use front\library\WeChat;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SiteController
 * @package front\api
 */
class SiteController extends Controller
{
    /**
     * default route
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionDefault(Request $request, Response $response)
    {
        return $response->withRedirect('/index.html');
    }

    /**
     * 获取验证码
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionVerification(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['mobile'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $mobile = $post['mobile'];
        //手机号码验证
        if(!$this->checkMobile($mobile)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '手机号码格式错误']));
        }
        //msg
        $msg = '欢迎注册药学工具网账号，验证码为： ';
        if (isset($post['reset']) && $post['reset'] == 1) {
            $msg = '您正在重置药学工具网账号，验证码为： ';
        } else if (isset($post['change']) && $post['change'] == 1) {
            $msg = '更换手机号,手机验证码： ';
        }

        //发码
        $verify_code = $this->smsVerifyCode($mobile, $msg);
        if ($verify_code != true) {
            $return = ['status'=>false, 'message'=>'验证码获取失败', 'code'=>$verify_code];
        } else {
            $return = ['status'=>true, 'message'=>''];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * 用户注册
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionRegister(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['mobile', 'password', 'nickname', 'verification'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        if(!$this->checkVerifyCode($post['mobile'], $post['verification'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'验证码错误或已过期']));
        }

        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2]])->fetch('id');
        if ($user_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户已存在']));
        }

        $insert_row = [
            'mobile' => $post['mobile'],
            'password' => md5($post['password']),
            'nickname' => $post['nickname'],
            'unionid' => $post['unionid'] ? $post['unionid'] : '',
            'code' => $this->getCode(),
            'create_time' => time()
        ];
        $this->db->transaction = 'BEGIN';
        $result = $this->db->user_global()->insert($insert_row);
        $insert_id = $this->db->user_global()->insert_id();
        $result_code_insert = true;
        $result_user_global_update = true;
        $result_score_details_insert = true;
        //邀请码逻辑
        if (!empty($post['code'])) {
            $user_global_info = $this->db->user_global()->where(['code'=>$post['code'], 'status'=>1])->fetch();
            if (!$user_global_info) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'邀请码不可用']));
            }
            $user_global_info = iterator_to_array($user_global_info->getIterator());
            $code_insert_row = [
                'from' => $user_global_info['id'],
                'to' => $insert_id,
                'code' => $post['code'],
                'score' => UserCodeController::CODE_SCORE,
                'create_time' => time()
            ];
            $result_code_insert = $this->db->user_code()->insert($code_insert_row);
            $score_detail_insert = $this->db->score_details()->insert([
                'uid' => $user_global_info['id'],
                'score' => UserCodeController::CODE_SCORE,
                'remark' => '邀请码',
                'type' => ScoreDetailsController::TYPE_FOUR,
                'create_time' => time()
            ]);
            $result_score_details_insert = $this->db->score_details()->insert($score_detail_insert);
            $score = $user_global_info['score'] + UserCodeController::CODE_SCORE;
            $result_user_global_update = $this->db->user_global()->where(['id'=>$user_global_info['id']])->update(['score'=>$score]);
        }
        if ($result !== false && $result_code_insert !== false
            && $result_user_global_update !== false && $result_score_details_insert !== false
        ) {
            $this->db->transaction = 'COMMIT';
            $uid_wechat = $this->db->user_wechat()->where(['unionid'=>$post['unionid']])->fetch('uid');
            $uid_wechat ? : $this->db->user_wechat()->where(['unionid'=>$post['unionid']])->update(['uid'=>$insert_id]);
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionReset(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['mobile', 'password', 'verification'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        if(!$this->checkVerifyCode($post['mobile'], $post['verification'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'验证码错误或已过期']));
        }

        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2]])->fetch('id');
        if ($user_id) {
            //存在 重置
            $update_row = [
                'password' => md5($post['password'])
            ];
            $result = $this->db->user_global()->where(['id'=>$user_id])->update($update_row);
            $return = $result !== false ? ['status' => true, 'message' => '重置成功'] : ['status' => false, 'message' => '重置失败'];
        } else {
            $return = ['status'=>false, 'message'=>'账户不存在'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
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
        if (!$this->formControlEmpty(['mobile', 'password'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $user_info = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>1])->fetch();
        if ($user_info) {
            if (md5($post['password']) === $user_info['password']) {
                $return = [
                    'status' => true,
                ];
                $user_info = iterator_to_array($user_info->getIterator());
                unset($user_info['password']);
                //登录成功
                $this->setLogin('_ys_front_login', $user_info, $request);
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
     * 账户信息
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionAccount(Request $request, Response $response)
    {
        $current_user_info = $this->currentUserInfo('_ys_front_login', $request);
        $return = ['status'=>false, 'message'=>'账户信息不存在', 'data'=>[]];
        if ($current_user_info) {
            $return = ['status'=>true, 'message'=>'', 'data'=>$current_user_info];
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

    /**
     * 检测绑定
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionCheckBind(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        if (!$this->formControlEmpty(['code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $code = $post['code'];
        $wechat_login_info = WeChat::getInfoByCode($code);
        if (!empty($wechat_login_info['errcode']) || !empty($wechat_login_info['errmsg'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>$wechat_login_info['errmsg']]));
        }
        //微信用户信息存储
        $uid_wechat = $this->db->user_wechat()->where(['unionid'=>$wechat_login_info['unionid']])->fetch();
        $uid_wechat ? : $this->db->user_wechat()->insert(['unionid'=>$wechat_login_info['unionid'], 'openid'=>$wechat_login_info['openid'],'create_time'=>time()]);

        //刷新&&存储access_token
        $refresh_result = WeChat::refreshToken($wechat_login_info['refresh_token']);
        $this->cacheToken($wechat_login_info['unionid'],$refresh_result);

        $user_info = $this->db->user_global()->where(['unionid' => $wechat_login_info['unionid'], 'status'=>1])->fetch();
        $user_info_other = $this->db->user_global()->where(['unionid' => $wechat_login_info['unionid'], 'status'=>[2,3]])->fetch();
        if ($user_info) {
            //绑定直接登录
            $user_info = iterator_to_array($user_info->getIterator());
            unset($user_info['password']);
            //登录成功
            $this->setLogin('_ys_front_login', $user_info, $request);
            $return = [
                'status' => true,
                'code' => 0,
                'message' => '',
            ];
        } else if ($user_info_other) {
            $return = [
                'status'=>true,
                'code' => 2,
                'message'=>'禁用/删除',
            ];
        } else {
            //未绑定返回json
            //获取微信用户详细
            $wechat_user_info = WeChat::userInfo($refresh_result['access_token'], $refresh_result['openid']);
            $return = [
                'status'=>true,
                'code' => 1,
                'message'=>'未绑定',
                'data'=>['user_info'=>$wechat_user_info]
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * 绑定
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function actionBind(Request $request, Response $response)
    {
        //绑定成功直接登录
        $post = $request->getParsedBody();

        if (!$this->formControlEmpty(['mobile','password','unionid'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $user_info_union = $this->db->user_global()->where(['unionid' => $post['unionid']])->fetch();
        if ($user_info_union) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'已绑定']));
        }

        $user_info = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>1])->fetch();
        if ($user_info) {
            if (md5($post['password']) === $user_info['password']) {
                $user_info = iterator_to_array($user_info->getIterator());
                //绑定
                $bind_result = $this->db->user_global()->where(['id'=>$user_info['id']])->update(['unionid' => $post['unionid']]);
                if ($bind_result) {
                    unset($user_info['password']);
                    //成功
                    $this->setLogin('_ys_front_login', $user_info, $request);
                    //微信用户信息存储
                    $uid_wechat = $this->db->user_wechat()->where(['unionid'=>$post['unionid']])->fetch('uid');
                    !$uid_wechat ?  : $this->db->user_wechat()->where(['unionid'=>$post['unionid']])->update(['uid'=>$user_info['id']]);
                    $return = [
                        'status' => true,
                    ];
                } else {
                    $return = [
                        'status' => false,
                        'message' => "绑定失败"
                    ];
                }
            } else {
                $return = [
                    'status' => false,
                    'message' => "密码错误"
                ];
            }
        } else {
            $return = [
                'status' => false,
                'message' => "不存在/已禁用"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    private function getCode()
    {
        $code = Tools::invitationCode(5);
        $user_global_id = $this->db->user_global()->where(['code'=>$code, 'status'=>1])->fetch('id');
        if ($user_global_id) {
            return $this->getCode();
        }
        return $code;
    }
}