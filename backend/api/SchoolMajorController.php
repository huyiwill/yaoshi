<?php

namespace backend\api;

use backend\library\Tools;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SchoolMajorController
 * @package backend\api
 */
class SchoolMajorController extends Controller
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
        if (!$this->formControlEmpty(['pid','name', 'code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $school_major_id = $this->db->school_major()->where(['name' => $post['name'], 'status'=>[1,2]])->fetch('id');
        if ($school_major_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'专业名称已存在']));
        }
        $insert_row = [
            'pid' => $post['pid'],
            'name' => $post['name'],
            'code' => $post['code'],
            'create_time' => time()
        ];
        $result = $this->db->school_major()->insert($insert_row);
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

        $school_major_id = $this->db->school_major()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($school_major_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'专业不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->school_major()->where(['id' => $post['id']])->update($update_row);
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
        $result = $this->db->school_major()->where(['id'=>$post['id']])->update($update_row);
        //exits ?
        $result_relation = true;
        $this->deleteChildCategory($post['id']);

        if ($result && $result_relation) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 递归删除*/
    private function deleteChildCategory($category_id)
    {
        $relation_list = $this->iterator_array($this->db->school_major()->select('id,pid')->where(['pid'=>$category_id,'status'=>[1,2]]));
        if (!empty($relation_list)) {
            $this->db->school_major()->where(['pid'=>$category_id])->update(['status'=>3]);
            foreach ($relation_list as $key=>$item) {
                $this->deleteChildCategory($item['id']);
            }
        } else {
            return true;
        }
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','pid', 'name', 'code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $school_major_id = $this->db->school_major()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($school_major_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'专业不存在']));
        }

        $update_row = [
            'pid' => $post['pid'],
            'name' => $post['name'],
            'code' => $post['code'],
        ];
        $result = $this->db->school_major()->where(['id' => $post['id']])->update($update_row);
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
        $count = $this->db->school_major()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->school_major()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $school_major_info = $this->db->school_major()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($school_major_info) {
            $school_major_info = iterator_to_array($school_major_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $school_major_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "专业不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->school_major()->where(['status'=>1])->order('id asc')->select('id,pid,name'));
        if ($result) {
            $result = Tools::getTree($result);
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

    public function actionOptions(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->school_major()->where(['status'=>1])->order('id asc')->select('id,pid,name'));
        if ($result) {
            $result = Tools::getOptions($result);
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