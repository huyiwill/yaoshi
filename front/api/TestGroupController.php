<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class TestGroupController
 * @package front\api
 */
class TestGroupController extends Controller
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
        if (!$this->formControlEmpty(['name'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_group_id = $this->db->test_group()->where(['name' => $post['name'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1,2]])->fetch('id');
        if ($test_group_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'名称已存在']));
        }
        $insert_row = [
            'uid_admin' => $cache_user_info['id'],
            'name' => $post['name'],
            'create_time' => time()
        ];
        $result = $this->db->test_group()->insert($insert_row);
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
        $result = $this->db->test_group()->where(['id'=>$post['id'],'uid_admin' => $cache_user_info['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'name'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $test_group_id = $this->db->test_group()->where(['id' => $post['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch('id');
        if (empty($test_group_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'分组不存在']));
        }
        $update_row = [
            'name' => $post['name'],
        ];
        $result = $this->db->test_group()->where(['id'=>$post['id']])->update($update_row);
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
            'name' => @$get['name'] ? $get['name'] : ''
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->test_group()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->test_group()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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
        $test_group_info = $this->db->test_group()->where(['id' => $get['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch();
        if ($test_group_info) {
            $test_group_info = iterator_to_array($test_group_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $test_group_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "分组不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionDrop(Request $request, Response $response)
    {
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $result = $this->iterator_array($this->db->test_group()->where(['uid_admin' => $cache_user_info['id'],'status'=>1])->order('id asc')->select('id,name'));
        if (!empty($result)) {
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