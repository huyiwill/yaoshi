<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 历年真题
 * Class ExercisesController
 * @package backend\api
 */
class ExercisesController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['name','time','remark','price','test_end','role', 'photo'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        //药师职务检测
        if ($post['role'] == UserController::ROLE_TWO && empty($post['post'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $exercises_id = $this->db->exercises()->where(['name' => $post['name'], 'status'=>[1,2]])->fetch('id');
        if ($exercises_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'名称已存在']));
        }

        $insert_row = [
            'name' => $post['name'],
            'time' => $post['time'],
            'remark' => $post['remark'],
            'price' => $post['price'],
            'is_discount' => $post['is_discount'] ? $post['is_discount'] : 2,
            'discount' => $post['discount'] ? $post['discount'] : '',
            'discount_end' => $post['discount_end'] ? strtotime($post['discount_end']) : 0,
            'test_end' => strtotime($post['test_end']),
            'role' => $post['role'],
            'post' => $post['post'] ? $post['post'] : 0,
            'create_time' => time()
        ];

        //处方图片处理
        $uploadResult = $this->uploadBase64Image($post['photo'], '', 'exercises', 'exercises_logo');
        if (!empty($uploadResult)) {
            //上传新图片
            $insert_row['real_photo'] = $uploadResult['realPath'];
            $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
        }
        $result = $this->db->exercises()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
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

        $exercises_id = $this->db->exercises()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($exercises_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->exercises()->where(['id'=>$post['id']])->update($update_row);
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

        $update_row = [
            'status' => 3
        ];
        //关联删除历年真题下题目
        $this->db->transaction = 'BEGIN';
        $result = $this->db->exercises()->where(['id'=>$post['id']])->update($update_row);
        $check = $this->iterator_array($this->db->exercises_subject()->select('')->where(['exercises_id' => $post['id']])->limit(1));
        $result_delete = true;
        if (!empty($check)) {
            $result_delete = $this->db->exercises_subject()->where(['exercises_id' => $post['id']])->update($update_row);
        }
        if ($result && $result_delete) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 开启/关闭 */
    public function actionType(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'type'], $post) || !in_array($post['type'], [1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $exercises_id = $this->db->exercises()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($exercises_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        $update_row = [
            'type' => $post['type']
        ];
        $result = $this->db->exercises()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['name','time','remark','price','test_end','role'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        //药师职务检测
        if ($post['role'] == UserController::ROLE_TWO && empty($post['post'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $exercises_id = $this->db->exercises()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (!$exercises_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'真题不存在']));
        }

        $update_row = [
            'name' => $post['name'],
            'time' => $post['time'],
            'remark' => $post['remark'],
            'price' => $post['price'],
            'is_discount' => $post['is_discount'] ? $post['is_discount'] : 2,
            'discount' => $post['discount'] ? $post['discount'] : '',
            'discount_end' => $post['discount_end'] ? strtotime($post['discount_end']) : 0,
            'test_end' => strtotime($post['test_end']),
            'role' => $post['role'],
            'post' => $post['post'] ? $post['post'] : 0,
        ];

        if (!empty($post['photo'])) {
            $uploadResult = $this->uploadBase64Image($post['photo'], '', 'exercises', 'exercises_logo');
            if (!empty($uploadResult)) {
                //删除旧图片
                $exercises_info = $this->db->exercises()->where(['id'=>$post['id']])->fetch();
                if ($exercises_info) {
                    $exercises_info = iterator_to_array($exercises_info->getIterator());
                    @unlink($exercises_info['real_photo']);
                }
                //上传新图片
                $update_row['real_photo'] = $uploadResult['realPath'];
                $update_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
        }
        $result = $this->db->exercises()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
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
        $count = $this->db->exercises()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->exercises()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $exercises_info = $this->db->exercises()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($exercises_info) {
            $exercises_info = iterator_to_array($exercises_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $exercises_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "真题不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 预览 */
    public function actionPreview(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $order = 'sort asc';
        //总题数
        $count = $this->db->exercises_subject()->select('')
            ->where(['exercises_id' => $get['id'],'status'=>1])->count();
        //总分数
        $total = $this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $get['id'],'status'=>1]);
        $total = $total['total'];
        //单选题
        $subject_one = $this->iterator_array($this->db->exercises_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->order($order));
        //多选
        $subject_two = $this->iterator_array($this->db->exercises_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->order($order));
        //处方审核
        $subject_four = $this->iterator_array($this->db->exercises_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->order($order));
        //用药交代
        $subject_five = $this->iterator_array($this->db->exercises_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->order($order));
        //问答
        $subject_six = $this->iterator_array($this->db->exercises_subject()->select('id,name,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->order($order));
        if (!empty($subject_one)) {
            array_walk($subject_one, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }
        if (!empty($subject_two)) {
            array_walk($subject_two, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }
        $return = [
            'status' => true,
            'data' => [
                'count' => $count,
                'total' => $total,
                'subject_one' => $subject_one,
                'subject_two' => $subject_two,
                'subject_four' => $subject_four,
                'subject_five' => $subject_five,
                'subject_six' => $subject_six,
            ]
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}