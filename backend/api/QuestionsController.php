<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class QuestionsController
 * @package backend\api
 */
class QuestionsController extends Controller
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
        if (!$this->formControlEmpty(['pharmacy_id','name', 'code', 'occupation','topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $questions_id = $this->db->admin_questions()->where(['name' => $post['name'], 'status'=>[1,2]])->fetch('id');
        if ($questions_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题库已存在']));
        }
        //获取顶级分类
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        $insert_row = [
            'category_id' => $category_id,
            'pharmacy_id' => $post['pharmacy_id'],
            'name' => $post['name'],
            'code' => $post['code'],
            'occupation' => $post['occupation'],
            'topic_type' => $post['topic_type'],
            'create_time' => time()
        ];
        $result = $this->db->admin_questions()->insert($insert_row);
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

        $questions_id = $this->db->admin_questions()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($questions_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->admin_questions()->where(['id' => $post['id']])->update($update_row);
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
        $this->db->transaction = 'BEGIN';
        $result = $this->db->admin_questions()->where(['id' => $post['id']])->update($update_row);
        //多对多删除 ys_questions_subject
        $check = $this->iterator_array($this->db->questions_subject()->select('')->where(['questions_id' => $post['id']])->limit(1));
        $result_delete = true;
        if (!empty($check)) {
            $result_delete = $this->db->questions_subject()->where(['questions_id' => $post['id']])->delete();
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
        //form control empty
        if (!$this->formControlEmpty(['id', 'category_id', 'pharmacy_id', 'name','code','occupation','topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $questions_id = $this->db->admin_questions()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($questions_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题库不存在']));
        }
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        $update_row = [
            'category_id' => $category_id,
            'pharmacy_id' => $post['pharmacy_id'],
            'name' => $post['name'],
            'code' => $post['code'],
            'occupation' => $post['occupation'],
            'topic_type' => $post['topic_type'],
        ];
        $result = $this->db->admin_questions()->where(['id' => $post['id']])->update($update_row);
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
        $count = $this->db->admin_questions()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->admin_questions()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $questions_info = $this->db->admin_questions()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($questions_info) {
            $questions_info = iterator_to_array($questions_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $questions_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "题库不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
    
    public function actionDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->admin_questions()->where(['status'=>1])->order('id asc')->select('id','name'));
        if ($result) {
            $return = [
                'status' => true,
                'data' => $result,
                'message' => '获取成功'
            ];
        } else {
            $return = [
                'status' => false,
                'message' => '数据错误'
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}