<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class OrderController
 * @package backend\api
 */
class OrderController extends Controller
{
    const TYPE_ONE = 1; //积分购买
    const TYPE_TWO = 2; //会员办理
    const TYPE_THREE = 3; //历年真题

    const DEDUCTIBLE_ONE = 1; //抵扣
    const DEDUCTIBLE_TWO = 2; //不抵扣

    const STATE_ONE = 1; //未支付
    const STATE_TWO = 2; //支付成功

    const SCORE_RATIO = 10; // money : score = 1 : 10

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* info */
    public function actionInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $order_info = $this->db->order()->where(['id' => $get['id']])->fetch();
        if ($order_info) {
            $order_info = iterator_to_array($order_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $order_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "订单不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 订单列表 type: 产品类型 1:积分购买,2:会员办理,3:历年真题购买 */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($get, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->order()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->order()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $data,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}