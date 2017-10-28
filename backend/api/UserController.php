<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class UserController
 * @package backend\api
 */
class UserController extends Controller
{
    const ROLE_ONE = 1; //其他
    const ROLE_TWO = 2; //药师
    const ROLE_THREE = 3; //医生
    const ROLE_FOUR = 4; //护士
    const ROLE_FIVE = 5; //学生

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //public form control empty
        if (!$this->formControlEmpty(['mobile','password','nickname','name','head','role','group'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        //注册过
        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2]])->fetch('id');
        if ($user_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户已存在']));
        }
        $this->db->transaction = 'BEGIN';
        $base_insert_row = [
            'name' => $post['name'],
            'mobile' => $post['mobile'],
            'password' => md5($post['password']),
            'nickname' => $post['nickname'],
            'perfect' => 2,
            'role' => $post['role'] ? $post['role'] : 1,
            'birthday' => $post['birthday'] ? strtotime($post['birthday']) : 0,
            'code' => $post['code'] ? $post['code'] : '',
            '`group`' => $post['group'],
            '`from`' => 2,
            'create_time' => time()
        ];
        //头像处理
        $uploadHead = $this->uploadBase64Image($post['head'], $post['mobile'], 'user/head', 'user_head');
        if (!empty($uploadHead)) {
            //上传新图片
            $base_insert_row['real_head'] = $uploadHead['realPath'];
            $base_insert_row['head'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadHead['photo'];
        }
        //资格证书照片
        if (!empty($post['photo'])) {
            $uploadResult = $this->uploadBase64Image($post['photo'], $post['mobile'], 'user/auth', 'user_auth');
            if (!empty($uploadResult)) {
                //上传新图片
                $base_insert_row['real_path'] = $uploadResult['realPath'];
                $base_insert_row['path'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
        }
        $base_result = $this->db->user_global()->insert($base_insert_row);
        $base_insert_id = $this->db->user_global()->insert_id();
        $result = false;
        if ($post['role'] == self::ROLE_ONE) {
            //other
            $result = $this->insertOther($base_insert_id, $response,$post);
        } else if ($post['role'] == self::ROLE_TWO) {
            //药师
            $result = $this->insertPharmacist($base_insert_id,$response, $post);
        } else if ($post['role'] == self::ROLE_THREE || $post['role'] == self::ROLE_FOUR) {
            //医生 || 护士
            $result = $this->insertHealth($base_insert_id,$response, $post);
        } else if ($post['role'] == self::ROLE_FIVE) {
            //学生
            $result = $this->insertStudent($base_insert_id,$response, $post);
        }
        if ($base_result != false && $result != false) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 禁用/启用 */
    public function actionStatus(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'status'], $post) || !in_array($post['status'], [1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $user_id = $this->db->user_global()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($user_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->user_global()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 删除 */
    public function actionDel(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $user_id = $this->db->user_global()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($user_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户不存在']));
        }
        $update_row = [
            'status' => 3
        ];
        $result = $this->db->user_global()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //public form control empty
        if (!$this->formControlEmpty(['id','mobile', 'nickname','name', 'group'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        //手机号验证重复
        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2], 'Not id' => $post['id']])->fetch('id');
        if ($user_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'用户已存在']));
        }

        //用户信息
        $user_info = $this->db->user_global()->where(['id'=>$post['id'], 'status'=>[1,2]])->fetch();
        if (!$user_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'用户不存在']));
        }
        $user_info = iterator_to_array($user_info->getIterator());

        $this->db->transaction = 'BEGIN';
        $base_update_row = [
            'name' => $post['name'],
            'mobile' => $post['mobile'],
            'nickname' => $post['nickname'],
            'role' => $post['role'] ? $post['role'] : 1,
            'birthday' => $post['birthday'] ? strtotime($post['birthday']) : 0,
            'code' => $post['code'] ? $post['code'] : '',
            '`group`' => $post['group'],
        ];
        //密码处理
        if (!empty($post['password'])) {
            $base_update_row['password'] = md5($post['password']);
        }
        //头像处理
        if (!empty($post['head'])) {
            $uploadHead = $this->uploadBase64Image($post['head'], $post['mobile'], 'user/head', 'user_head');
            if (!empty($uploadHead)) {
                //删除原图片
                @unlink($user_info['real_head']);
                //上传新图片
                $base_update_row['real_head'] = $uploadHead['realPath'];
                $base_update_row['head'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadHead['photo'];
            }
        }
        //资格证书照片
        if (!empty($post['photo'])) {
            $uploadResult = $this->uploadBase64Image($post['photo'], $post['mobile'], 'user/auth', 'user_auth');
            if (!empty($uploadResult)) {
                //删除原图片
                @unlink($user_info['real_path']);
                //上传新图片
                $base_update_row['real_path'] = $uploadResult['realPath'];
                $base_update_row['path'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
        }
        $base_result = $this->db->user_global()->where(['id'=>$post['id']])->update($base_update_row);
        $result = false;
        if ($user_info['role'] == self::ROLE_ONE) {
            //other
            $result = $this->updateOther($user_info['id'], $response,$post);
        } else if ($user_info['role'] == self::ROLE_TWO) {
            //药师
            $result = $this->updatePharmacist($user_info['id'],$response, $post);
        } else if ($user_info['role'] == self::ROLE_THREE || $post['role'] == self::ROLE_FOUR) {
            //医生 || 护士
            $result = $this->updateHealth($user_info['id'],$response, $post);
        } else if ($user_info['role'] == self::ROLE_FIVE) {
            //学生
            $result = $this->updateStudent($user_info['id'],$response, $post);
        }
        if ($base_result !== false && $result !== false) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* list */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($get, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->user_global()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->user_global()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* info */
    public function actionInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $user_info = $this->db->user_global()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($user_info) {
            $user_info = iterator_to_array($user_info->getIterator());
            //用户补充信息
            //根据角色获取
            switch ($user_info['role']) {
                case self::ROLE_ONE:
                    //其他
                    $perfect_info = $this->db->user_other()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case self::ROLE_TWO:
                    //药师
                    $perfect_info = $this->db->user_pharmacist()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case self::ROLE_THREE || UserController::ROLE_FOUR:
                    //医生
                    $perfect_info = $this->db->user_health()->where(['uid'=>$user_info['id']])->fetch();
                    break;
                case self::ROLE_FIVE:
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

    private function insertOther($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['city', 'company'], $post)) {
            return false;
        }
        //去重
        if ($this->db->user_other()->where(['uid'=>$uid])->fetch('id')){
            return false;
        }
        $insert_row['uid'] = $uid;
        $insert_row['city'] = $post['city'];
        $insert_row['company'] = $post['company'];
        $insert_row['post'] = $post['post'] ? $post['post'] : '';
        $insert_row['create_time'] = time();
        return $this->db->user_other()->insert($insert_row);
    }

    private function insertPharmacist($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['post', 'hospital'], $post)) {
            return false;
        }
        //去重
        if ($this->db->user_pharmacist()->where(['uid'=>$uid])->fetch('id')){
            return false;
        }
        $insert_row['uid'] = $uid;
        $insert_row['post'] = $post['post'];
        $insert_row['hospital'] = $post['hospital'];
        $insert_row['title'] = $post['title'] ? $post['title'] : '';
        $insert_row['teaching_hospital'] = $post['teaching_hospital'] ? $post['teaching_hospital'] : '';
        $insert_row['create_time'] = time();
        return $this->db->user_pharmacist()->insert($insert_row);
    }

    private function insertHealth($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['hospital', 'department'], $post)) {
            return false;
        }
        //去重
        if ($this->db->user_health()->where(['uid'=>$uid])->fetch('id')){
            return false;
        }
        $insert_row['uid'] = $uid;
        $insert_row['hospital'] = $post['hospital'];
        $insert_row['department'] = $post['department'];
        $insert_row['title'] = $post['title'] ? $post['title'] : '';
        $insert_row['teaching_hospital'] = $post['teaching_hospital'] ? $post['teaching_hospital'] : '';
        $insert_row['teaching_department'] = $post['teaching_department'] ? $post['teaching_department'] : '';
        $insert_row['create_time'] = time();
        return $this->db->user_health()->insert($insert_row);
    }

    private function insertStudent($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['entrance_time', 'school', 'major'], $post)) {
            return false;
        }
        //去重
        if ($this->db->user_student()->where(['uid'=>$uid])->fetch('id')){
            return false;
        }
        $insert_row['uid'] = $uid;
        $insert_row['entrance_time'] = strtotime($post['entrance_time']);
        $insert_row['school'] = $post['school'];
        $insert_row['major'] = $post['major'];
        $insert_row['education'] = in_array($post['education'], [1,2,3,4]) ? $post['education'] : 1;
        $insert_row['create_time'] = time();
        return $this->db->user_student()->insert($insert_row);
    }

    private function updateOther($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['city', 'company'], $post)) {
            return false;
        }
        $update_row['city'] = $post['city'];
        $update_row['company'] = $post['company'];
        $update_row['post'] = $post['post'] ? $post['post'] : '';
        return $this->db->user_other()->where(['uid'=>$uid])->update($update_row);
    }

    private function updatePharmacist($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['post', 'hospital'], $post)) {
            return false;
        }
        $update_row['post'] = $post['post'];
        $update_row['hospital'] = $post['hospital'];
        $update_row['title'] = $post['title'] ? $post['title'] : '';
        $update_row['teaching_hospital'] = $post['teaching_hospital'] ? $post['teaching_hospital'] : '';
        return $this->db->user_pharmacist()->where(['uid'=>$uid])->update($update_row);
    }

    private function updateHealth($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['hospital', 'department'], $post)) {
            return false;
        }
        $update_row['hospital'] = $post['hospital'];
        $update_row['department'] = $post['department'];
        $update_row['title'] = $post['title'] ? $post['title'] : '';
        $update_row['teaching_hospital'] = $post['teaching_hospital'] ? $post['teaching_hospital'] : '';
        $update_row['teaching_department'] = $post['teaching_department'] ? $post['teaching_department'] : '';
        return $this->db->user_health()->where(['uid'=>$uid])->update($update_row);
    }

    private function updateStudent($uid,Response $response,$post)
    {
        //form control empty
        if (!$this->formControlEmpty(['entrance_time', 'school', 'major'], $post)) {
            return false;
        }
        $update_row['entrance_time'] = strtotime($post['entrance_time']);
        $update_row['school'] = $post['school'];
        $update_row['major'] = $post['major'];
        $update_row['education'] = in_array($post['education'], [1,2,3,4]) ? $post['education'] : 1;
        return $this->db->user_student()->where(['uid'=>$uid])->update($update_row);
    }
}