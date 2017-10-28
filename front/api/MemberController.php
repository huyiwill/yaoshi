<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class MemberController
 * @package front\api
 */
class MemberController extends Controller
{
    const METHOD_ONE = 1; //线上/前台
    const METHOD_TWO = 2; //会员卡/后台

    const TIME_TYPE_ONE = 1; //按月
    const TIME_TYPE_TWO = 2; //按年

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* 会员卡开通 */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['number'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $member_card_info = $this->db->member_card()->where(['card_number'=>$post['number'],'state'=>1,'status'=>1])->fetch();
        if (!$member_card_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'会员卡不存在']));
        }
        $member_card_info = iterator_to_array($member_card_info->getIterator());
        $member_card_type_info = $this->db->member_card_type()->where(['id' => $member_card_info['type_id']])->fetch();
        if (!$member_card_type_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'会员类型不存在']));
        }
        $member_card_type_info = iterator_to_array($member_card_type_info->getIterator());
        $cache_user_info = $this->getLogin('_ys_front_login', $request);

        $str = $member_card_type_info['time_type'] == MemberController::METHOD_ONE ? ' months' : ' years';
        //未过期的会员
        $member_info = $this->iterator_array($this->db->member()->select('')->where(['uid'=>$cache_user_info['id'],'expiration_time >= ?'=>time()])->order('expiration_time desc')->limit(1));
        $str = '+ ' . $member_card_type_info['number'] . $str;
        if (empty($member_info)) {
            //新会员
            $expiration_time = strtotime($str);
        } else {
            //续费会员
            $expiration_time = strtotime($str, $member_info[0]['expiration_time']);
        }

        $insert_row = [
            'uid' => $cache_user_info['id'],
            'method' => MemberController::METHOD_TWO,
            'time_type' => $member_card_type_info['time_type'],
            'number' => $member_card_type_info['number'],
            'expiration_time' => $expiration_time,
            'status' => 1,
            'create_time' => time()
        ];
        $this->db->transaction = 'BEGIN';
        $result = $this->db->member()->insert($insert_row);
        //修改会员卡状态 $member_card_info
        $member_card_update_result = $this->db->member_card()->where(['id'=>$member_card_info['id']])->update(['state'=>2]);
        if ($result !== false && $member_card_update_result !== false) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* check */
    public static function check($uid)
    {
        $member_info = Controller::static_iterator_array(Controller::$db_static->member()->select('')->where(['uid'=>$uid,'expiration_time >= ?'=>time()])->order('expiration_time desc')->limit(1));
        if (empty($member_info)) {
            return false;
        }
        return true;
    }

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
        $count = $this->db->member()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->member()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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