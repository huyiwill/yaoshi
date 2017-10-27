<?php

namespace front\api;

use front\library\WeChat;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class MyController
 * @package front\api
 */
class MyController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* info */
    public function actionInfo(Request $request, Response $response)
    {
        $user_info = $this->currentUserInfo('_ys_front_login', $request);
        if ($user_info) {
            //用户补充信息
            //根据角色获取
            switch ($user_info['role']) {
                case UserController::ROLE_ONE:
                    //其他
                    $perfect_info = $this->db->user_other()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case UserController::ROLE_TWO:
                    //药师
                    $perfect_info = $this->db->user_pharmacist()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case UserController::ROLE_THREE || UserController::ROLE_FOUR:
                    //医生
                    $perfect_info = $this->db->user_health()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case UserController::ROLE_FIVE:
                    //学生
                    $perfect_info = $this->db->user_student()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                default:
                    return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'错误数据']));
                    break;
            }
            unset($user_info['password']);
            $user_info['perfect'] = [];
            //完善信息
            if ($perfect_info) {
                $user_info['perfect'] = iterator_to_array($perfect_info->getIterator());
            }
            $return = [
                'status' => true,
                'message' => '',
                'data' => $user_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "用户不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 安全设置 */
    public function actionPassword(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['password','new'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $password = $this->db->user_global()->where(['id'=>$cache_user_info['id']])->fetch('password');
        if (empty($password)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法数据']));
        }
        if (md5($post['password']) != $password) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'原密码错误']));
        }
        $update_row = [
            'password' => md5($post['new']),
        ];
        $result = $this->db->user_global()->where(['id'=>$cache_user_info['id']])->update($update_row);
        $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 更改手机 */
    public function actionMobile(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['mobile', 'verification'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        if(!$this->checkVerifyCode($post['mobile'], $post['verification'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'验证码错误或已过期']));
        }

        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2]])->fetch('id');
        if ($user_id) {
            //存在 重置
            $update_row = [
                'mobile' => $post['mobile']
            ];
            $result = $this->db->user_global()->where(['id'=>$user_id])->update($update_row);
            $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        } else {
            $return = ['status'=>false, 'message'=>'账户不存在'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 微信绑定 */
    public function actionWechat(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        if (!$this->formControlEmpty(['code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $code = $post['code'];
        $wechat_info = WeChat::getInfoByCode($code);
        if (!empty($wechat_info['errcode']) || !empty($wechat_info['errmsg'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>$wechat_info['errmsg']]));
        }

        $user_wechat = $this->db->user_wechat()->where(['unionid'=>$wechat_info['unionid']])->fetch();
        if ($user_wechat) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'该微信已绑定账号']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $update_row = [
            'unionid' => $wechat_info['unionid']
        ];
        $result = $this->db->user_global()->where(['id'=>$cache_user_info['id']])->update($update_row);
        $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 微信解除绑定 */
    public function actionWechatCancel(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        if (!$this->formControlEmpty(['code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $code = $post['code'];
        $wechat_info = WeChat::getInfoByCode($code);
        if (!empty($wechat_info['errcode']) || !empty($wechat_info['errmsg'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>$wechat_info['errmsg']]));
        }

        $user_wechat = $this->db->user_wechat()->where(['unionid'=>$wechat_info['unionid']])->fetch();
        if (!$user_wechat) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'该微信未绑定账号']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $update_row = [
            'unionid' => ''
        ];
        $result = $this->db->user_global()->where(['id'=>$cache_user_info['id']])->update($update_row);
        $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 审核题目列表 */
    public function actionExamineList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->subject_examine()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->subject_examine()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* examine info */
    public function actionExamineInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $subject_examine_info = $this->db->subject_examine()->where(['id' => $get['id'], 'uid' => $cache_user_info['id'], 'status'=>[1]])->fetch();
        if ($subject_examine_info) {
            $subject_examine_info = iterator_to_array($subject_examine_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $subject_examine_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "题目不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* examine add */
    public function actionExamineAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $public_form = ['right_key','role','degree','pharmacy_id','analysis'];
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if ($post['topic_type'] == SubjectController::SUBJECT_ONE || $post['topic_type'] == SubjectController::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'uid' => $cache_user_info['id'],
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'create_time' => time(),
            ];
            $result = $this->db->subject_examine()->insert($insert_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_FOUR || $post['topic_type'] == SubjectController::SUBJECT_FIVE) {
            //处方 || 用药交代
            if (!$this->formControlEmpty(array_merge(['photo'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'uid' => $cache_user_info['id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'create_time' => time(),
            ];

            //处方图片处理
            $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject/examine', 'subject_examine');
            if (!empty($uploadResult)) {
                //上传新图片
                $insert_row['real_photo'] = $uploadResult['realPath'];
                $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
            $result_subject = $this->db->subject_examine()->insert($insert_row);
            $result_subject_id = $this->db->subject_examine()->insert_id();
            //患者信息提取
            $insert_patient = [
                'name' => $post['patient_name'] ? $post['patient_name'] : 'name_'.$result_subject_id,
                'subject_id' => $result_subject_id,
                'sex' => $post['sex'] ? $post['sex'] : 1,
                'age' => $post['age'] ? $post['age'] : 0,
                'department' => $post['department'] ? $post['department'] : '',
                'clinical_diagnosis' => $post['clinical_diagnosis'] ? $post['clinical_diagnosis'] : '',
                'medicine' => $post['medicine'] ? $post['medicine'] : '',
                'state' => 2,
                'create_time' => time()
            ];
            $result_patient = $this->db->patient()->insert($insert_patient);
            if ($result_subject && $result_patient) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else if ($post['topic_type'] == SubjectController::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'uid' => $cache_user_info['id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'create_time' => time(),
            ];
            $result = $this->db->subject_examine()->insert($insert_row);
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }

        $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* examine update */
    public function actionExamineUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $public_form = ['right_key','role','degree','pharmacy_id','analysis'];
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $subject_examine_info = $this->db->subject_examine()
            ->where(['id'=>$post['id'], 'uid'=>$cache_user_info['id'], 'status'=>1])->fetch();
        if (!$subject_examine_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }
        $subject_examine_info = iterator_to_array($subject_examine_info->getIterator());
        if ($subject_examine_info['state'] == 2) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }

        if ($post['topic_type'] == SubjectController::SUBJECT_ONE || $post['topic_type'] == SubjectController::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $update_row = [
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
            ];
            $result = $this->db->subject_examine()->where(['id'=>$post['id']])->update($update_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_FOUR || $post['topic_type'] == SubjectController::SUBJECT_FIVE) {
            //处方 || 用药交代
            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
            ];

            //处方图片处理
            if (!empty($post['photo'])) {
                $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject/examine', 'subject_examine');
                if (!empty($uploadResult)) {
                    //删除
                    @unlink($subject_examine_info['real_photo']);
                    //上传新图片
                    $insert_row['real_photo'] = $uploadResult['realPath'];
                    $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
                }
            }

            $this->db->transaction = 'BEGIN';
            $result_subject = $this->db->subject_examine()->where(['id'=>$post['id']])->update($update_row);
            //删除对应的患者信息
            $result_delete = $this->db->patient()->where(['subject_id'=>$post['id']])->delete();
            //患者信息提取
            $insert_patient = [
                'name' => $post['patient_name'] ? $post['patient_name'] : 'name_'.$post['id'],
                'subject_id' => $post['id'],
                'sex' => $post['sex'] ? $post['sex'] : 1,
                'age' => $post['age'] ? $post['age'] : 0,
                'department' => $post['department'] ? $post['department'] : '',
                'clinical_diagnosis' => $post['clinical_diagnosis'] ? $post['clinical_diagnosis'] : '',
                'medicine' => $post['medicine'] ? $post['medicine'] : '',
                'create_time' => time()
            ];
            $result_patient = $this->db->patient()->insert($insert_patient);
            if ($result_subject && $result_patient && $result_delete) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else if ($post['topic_type'] == SubjectController::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
            ];
            $result = $this->db->subject_examine()->where(['id'=>$post['id']])->update($update_row);
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 练习记录 错题:/purpose/list/two.json, 收藏:/purpose/list/one.json */
    /* 历年真题记录 */
    public function actionExercisesDo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->exercises_do()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->exercises_do()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function (&$item) {
                $exercises_info = $this->db->exercises()->where(['id'=>$item['exercises_id']])->fetch();
                $item['exercises_info'] = $exercises_info ? iterator_to_array($exercises_info->getIterator()) : [];
            });
        }
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 我的考核 /front/assess/list.json */

    /* 我的会议  meeting_user */
    public function actionMeeting(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->meeting_user()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->meeting_user()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function (&$item) {
                $meeting_info = $this->db->meeting()->where(['id'=>$item['meeting_id']])->fetch();
                $item['meeting_info'] = $meeting_info ? iterator_to_array($meeting_info->getIterator()) : [];
            });
        }
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 账户中心 */
    /* 邀请码  /code/add.json /code/list.json */
    /* 我的订单 /order/list.json */
    /* 会员记录 /member/list.json */
    /* 我的积分记录 /score/list.json */

}