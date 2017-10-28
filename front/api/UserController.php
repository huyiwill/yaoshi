<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class UserController
 * @package front\api
 */
class UserController extends Controller
{
    const ROLE_ONE = 1; //其他
    const ROLE_TWO = 2; //药师
    const ROLE_THREE = 3; //医生
    const ROLE_FOUR = 4; //护士
    const ROLE_FIVE = 5; //学生

    /* 填写邀请码 */
    public function actionCode(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        //check
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $user_code_id = $this->db->user_code()->where(['to'=>$cache_user_info['id']])->fetch('id');
        if ($user_code_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不可重复填写']));
        }

        $user_global_id = $this->db->user_global()->where(['code'=>$post['code'], 'status'=>1])->fetch('id');
        if (!$user_global_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'邀请码不可用']));
        }

        $insert_row = [
            'from' => $user_global_id,
            'to' => $cache_user_info['id'],
            'code' => $post['code'],
            'score' => 0,
            'create_time' => time()
        ];
        $result = $this->db->user_code()->insert($insert_row);
        if ($result !== false) {
//            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
//            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 完善信息 */
    public function actionPerfect(Request $request, Response $response)
    {
        //新增
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['name', 'head', 'role'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $current_user_info = $this->currentUserInfo('_ys_front_login', $request);

        $this->db->transaction = 'BEGIN';
        $base_update_row['name'] = $post['name'];
        $base_update_row['birthday'] = $post['birthday'] ? strtotime($post['birthday']) : 0;
        $base_update_row['role'] = $post['role'] ? $post['role'] : 1;
        $base_update_row['perfect'] = 2;
        //头像处理
        $uploadHead = $this->uploadBase64Image($post['head'], $current_user_info['mobile'], 'user/head', 'user_head');
        if (empty($uploadHead)) {
            //不处理
            unset($post['head']);
        } else {
            //上传新图片
            $base_update_row['real_head'] = $uploadHead['realPath'];
            $base_update_row['head'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadHead['photo'];
        }
        //资格证书照片
        if (!empty($post['photo'])) {
            $uploadResult = $this->uploadBase64Image($post['photo'], $current_user_info['mobile'], 'user/auth', 'user_auth');
            if (empty($uploadResult)) {
                //不处理
                unset($post['path']);
            } else {
                //上传新图片
                $base_update_row['real_path'] = $uploadResult['realPath'];
                $base_update_row['path'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
        }
        $base_result = $this->db->user_global()->where(['id'=>$current_user_info['id']])->update($base_update_row);
        $result = false;
        if ($post['role'] == self::ROLE_ONE) {
            //other
            $result = $this->insertOther($current_user_info['id'],$response,$post);
        } else if ($post['role'] == self::ROLE_TWO) {
            //药师
            $result = $this->insertPharmacist($current_user_info['id'],$response, $post);
        } else if ($post['role'] == self::ROLE_THREE || $current_user_info['role'] == self::ROLE_FOUR) {
            //医生 || 护士
            $result = $this->insertHealth($current_user_info['id'],$response, $post);
        } else if ($post['role'] == self::ROLE_FIVE) {
            //学生
            $result = $this->insertStudent($current_user_info['id'],$response, $post);
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

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        $user_info = $this->currentUserInfo('_ys_front_login', $request);
        //public form control empty
        if (!$this->formControlEmpty(['mobile', 'nickname','name', 'group'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        //手机号验证重复
        $user_id = $this->db->user_global()->where(['mobile' => $post['mobile'], 'status'=>[1,2], 'Not id' => $user_info['id']])->fetch('id');
        if ($user_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'用户已存在']));
        }

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
        $base_result = $this->db->user_global()->where(['id'=>$user_info['id']])->update($base_update_row);
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