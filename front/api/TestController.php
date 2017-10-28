<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 试卷
 * Class TestController
 * @package front\api
 */
class TestController extends Controller
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
        if (!$this->formControlEmpty(['name','remark'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_id = $this->db->test()->where(['name' => $post['name'], 'uid_admin' => $cache_user_info['id'],'status'=>[1,2]])->fetch('id');
        if ($test_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'名称已存在']));
        }

        $insert_row = [
            'uid_admin' => $cache_user_info['id'],
            'test_group_id' => $post['test_group_id'] ? $post['test_group_id'] : 0,
            'name' => $post['name'],
            'remark' => $post['remark'],
            'create_time' => time()
        ];
        $result = $this->db->test()->insert($insert_row);
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
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $update_row = [
            'status' => 3
        ];
        //关联删除试卷下题目
        $this->db->transaction = 'BEGIN';
        $result = $this->db->test()->where(['id'=>$post['id'],'uid_admin'=>$cache_user_info['id']])->update($update_row);
        $check = $this->iterator_array($this->db->test_subject()->select('')->where(['test_id' => $post['id']])->limit(1));
        $result_delete = true;
        if (!empty($check)) {
            $result_delete = $this->db->test_subject()->where(['test_id' => $post['id']])->update($update_row);
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

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        if (!$this->formControlEmpty(['id','name','remark'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_id = $this->db->test()->where(['id' => $post['id'], 'uid_admin' => $cache_user_info['id'],'status'=>1])->fetch('id');
        if (!$test_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'数据不存在']));
        }

        $update_row = [
            'test_group_id' => $post['test_group_id'] ? $post['test_group_id'] : 0,
            'name' => $post['name'],
            'remark' => $post['remark'],
        ];
        $result = $this->db->test()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* list */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid_admin' => $cache_user_info['id'],
            'name' => $get['name'] ? $get['name'] : ''
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->test()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->test()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function (&$item) {
                $item['group_name'] = $this->db->test_group()->where(['id'=>$item['test_group_id']])->fetch('name');
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

    /* info */
    public function actionInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_info = $this->db->test()->where(['id' => $get['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>1])->fetch();
        if ($test_info) {
            $test_info = iterator_to_array($test_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $test_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "试卷不存在"
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
        //check
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_uid = $this->db->test()->where(['id'=>$get['id']])->fetch('uid_admin');
        if (empty($test_uid) || $test_uid != $cache_user_info['id']) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }
        $order = 'sort asc';
        //总题数
        $count = $this->db->test_subject()->select('')
            ->where(['test_id' => $get['id'],'status'=>1])->count();
        //总分数
        $total = $this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $get['id'],'status'=>1]);
        $total = $total['total'];
        //单选题
        $subject_one = $this->iterator_array($this->db->test_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['test_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->order($order));
        //多选
        $subject_two = $this->iterator_array($this->db->test_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['test_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->order($order));
        //处方审核
        $subject_four = $this->iterator_array($this->db->test_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['test_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->order($order));
        //用药交代
        $subject_five = $this->iterator_array($this->db->test_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['test_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->order($order));
        //问答
        $subject_six = $this->iterator_array($this->db->test_subject()->select('id,name,right_key,score,category_id,pharmacy_id')
            ->where(['test_id' => $get['id'],'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->order($order));
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