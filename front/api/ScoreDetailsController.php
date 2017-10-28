<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 积分明细
 * Class ScoreDetailsController
 * @package front\api
 */
class ScoreDetailsController extends Controller
{
    const TYPE_ONE = 1; //积分购买
    const TYPE_TWO = 2; //会员办理抵扣
    const TYPE_THREE = 3; //历年真题购买抵扣
    const TYPE_FOUR = 4; //邀请码

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->score_details()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->score_details()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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