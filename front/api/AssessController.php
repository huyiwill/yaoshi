<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class AssessController
 * @package front\api
 */
class AssessController extends Controller
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
        if (!$this->formControlEmpty(['name','test_id', 'pass_score', 'start_time', 'end_time', 'time'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_id = $this->db->assess()->where(['name' => $post['name'], 'uid_admin' => $cache_user_info['id'],'status'=>[1,2]])->fetch('id');
        if ($assess_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'考核名称已存在']));
        }
        $test_id = $this->db->test()->where(['id' => $post['test_id'], 'uid' => $cache_user_info['id'],'status'=>1])->fetch('id');
        if (!$test_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        $insert_row = [
            'uid_admin' => $cache_user_info['id'],
            'name' => $post['name'],
            'remark' => $post['remark'] ? $post['remark'] : '',
            'test_id' => $post['test_id'],
            'pass_score' => $post['pass_score'],
            'start_time' => strtotime($post['start_time']),
            'end_time' => strtotime($post['end_time']),
            'time' => $post['time'],
            'create_time' => time()
        ];
        $result = $this->db->assess()->insert($insert_row);
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
        $this->db->transaction = 'BEGIN';
        $result = $this->db->assess()->where(['id'=>$post['id'],'uid_admin' => $cache_user_info['id']])->update($update_row);
        //删除该考核下成员
        $check = $this->iterator_array($this->db->user_assess()->select('')->where(['assess_id' => $post['id']])->limit(1));
        $result_delete = true;
        if (!empty($check)) {
            $result_delete = $this->db->user_assess()->where(['assess_id' => $post['id']])->delete();
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
        if (!$this->formControlEmpty(['id','name','test_id', 'pass_score', 'start_time', 'end_time', 'time'], $post) || $post['start_time'] >= $post['end_time']) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_id = $this->db->assess()->where(['id' => $post['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch('id');
        if (empty($assess_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'成员不存在']));
        }
        $update_row = [
            'name' => $post['name'],
            'remark' => $post['remark'] ? $post['remark'] : '',
            'test_id' => $post['test_id'],
            'pass_score' => $post['pass_score'],
            'start_time' => strtotime($post['start_time']),
            'end_time' => strtotime($post['end_time']),
            'time' => $post['time'],
        ];
        $result = $this->db->assess()->where(['id'=>$post['id']])->update($update_row);
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
            'name' => @$get['name'] ? $get['name'] : '',
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->assess()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->assess()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_info = $this->db->assess()->where(['id' => $get['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch();
        if ($assess_info) {
            $assess_info = iterator_to_array($assess_info->getIterator());
            $assess_info['start_time'] = date('Y-m-d H:i:s', $assess_info['start_time']);
            $assess_info['end_time'] = date('Y-m-d H:i:s', $assess_info['end_time']);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $assess_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "考核不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 关联成员 */
    public function actionUserAssess(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['uid','assess_id'], $post) || !is_array($post['uid'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $i = 0;
        foreach ($post['uid'] as $key=>$item) {
            //check
            $user_assess_id = $this->db->user_assess()->where(['uid'=>$item,'assess_id'=>$post['assess_id'],'status'=>[1,2]])->fetch('id');
            if ($user_assess_id) {
                continue ;
            }
            $insert_row = [
                'uid' => $item,
                'assess_id' => $post['assess_id'],
                'create_time' => time()
            ];
            $result = $this->db->user_assess()->insert($insert_row);
            !$result ? : $i++;
        }
        $return = $i > 0 ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}