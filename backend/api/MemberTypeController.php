<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class MemberTypeController
 * @package backend\api
 */
class MemberTypeController extends Controller
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
        if (!$this->formControlEmpty(['name','time_type','number','unit_price'], $post)
            || !in_array($post['time_type'],[1,2])
        ) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        if (!empty($post['discount_fee'])) {
            $total_fee = $post['discount_fee'] * $post['number'];
        } else {
            $total_fee = $post['unit_price'] * $post['number'];
        }

        $member_card_type_id = $this->db->member_card_type()->where(['name' => $post['name'], 'status'=>[1,2]])->fetch('id');
        if ($member_card_type_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'类型已存在']));
        }
        $insert_row = [
            'name' => $post['name'],
            'time_type' => $post['time_type'],
            'number' => $post['number'],
            'unit_price' => $post['unit_price'],
            'discount_fee' => $post['discount_fee'] ? $post['discount_fee'] : 0,
            'total_fee' => $total_fee,
            'create_time' => time()
        ];
        $result = $this->db->member_card_type()->insert($insert_row);
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

        $member_card_type_id = $this->db->member_card_type()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($member_card_type_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'类型不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->member_card_type()->where(['id'=>$post['id']])->update($update_row);
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
        //关联删除类型下会员卡
        $this->db->transaction = 'BEGIN';
        $result = $this->db->member_card_type()->where(['id'=>$post['id']])->update($update_row);
        $member_card_update_result = $this->db->member_card()->where(['type_id'=>$post['id']])->update($update_row);
        if ($member_card_update_result != false && $result != false) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','name','time_type','number','unit_price'], $post)
            || !in_array($post['time_type'],[1,2])
        ) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        if (!empty($post['discount_fee'])) {
            $total_fee = $post['discount_fee'] * $post['number'];
        } else {
            $total_fee = $post['unit_price'] * $post['number'];
        }

        $member_card_type_id = $this->db->member_card_type()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($member_card_type_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'类型不存在']));
        }
        $update_row = [
            'name' => $post['name'],
            'time_type' => $post['time_type'],
            'number' => $post['number'],
            'unit_price' => $post['unit_price'],
            'discount_fee' => $post['discount_fee'] ? $post['discount_fee'] : 0,
            'total_fee' => $total_fee,
        ];
        $result = $this->db->member_card_type()->where(['id'=>$post['id']])->update($update_row);
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
        $count = $this->db->member_card_type()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->member_card_type()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $member_card_type_info = $this->db->member_card_type()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($member_card_type_info) {
            $member_card_type_info = iterator_to_array($member_card_type_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $member_card_type_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "类型不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->member_card_type()->where(['status'=>1])->order('id asc')->select('id,name'));
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