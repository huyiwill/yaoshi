<?php
namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class GroupController
 * @package backend\api
 */
class GroupController extends Controller{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];
    
    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['name', 'code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $group_id = $this->db->user_group()->where(['name' => $post['name'], 'status'=>[1,2]])->fetch('id');
        if ($group_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'分组已存在']));
        }
        $insert_row = [
            'name' => $post['name'],
            'code'=> $post['code'],
            'create_time' => time()
        ];
        $result = $this->db->user_group()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
    
    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','name', 'code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $group_id = $this->db->user_group()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($group_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'分组不存在']));
        }
        $update_row = [
            'name' => $post['name'],
            'code' => $post['code']
        ];
        
        $result = $this->db->user_group()->where(['id' => $post['id']])->update($update_row);
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
        $count = $this->db->user_group()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->user_group()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
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

        $group_id = $this->db->user_group()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($group_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'分组不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->user_group()->where(['id' => $post['id']])->update($update_row);
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
        $result = $this->db->user_group()->where(['id' => $post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
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

        $group_info = $this->db->user_group()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($group_info) {
            $group_info = iterator_to_array($group_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $group_info
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
        $result = $this->iterator_array($this->db->user_group()->where(['status'=>1])->order('id asc')->select('id,name'));
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