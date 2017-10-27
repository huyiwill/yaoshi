<?php

namespace front\api;

use front\library\Tools;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class OrderController
 * @package front\api
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

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if ((!$this->formControlEmpty(['product_subject','product_body','type', 'total_fee'], $post)) || !in_array($post['type'],[1,2,3])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        if ($post['type'] == self::TYPE_THREE && empty($post['relation_id'])) {
            //历年真题
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $current_user_info = $this->currentUserInfo('_ys_front_login', $request);
        $insert_row = [
            'product_subject' => $post['product_subject'],
            'product_body' => $post['product_body'],
            'type' => $post['type'],
            'relation_id' => empty($post['relation_id']) ? 0 : $post['relation_id']
        ];
        //check
        if ($post['type'] == self::TYPE_ONE) {
            //积分购买
            $insert_row['total_fee'] = $post['total_fee'];
            $insert_row['cash_payment'] = $post['total_fee'];
        } else if ($post['type'] == self::TYPE_TWO) {
            //会员办理
            if ((!$this->formControlEmpty(['time_type','number','is_deductible'], $post))) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            //优惠 312/month  原价 368/month
            $original_price = $post['time_type'] == MemberController::METHOD_ONE ? 368 * $post['number'] : 368 * 12 * $post['number'];;
            $discount_price = $post['time_type'] == MemberController::METHOD_ONE ? 0.01 * $post['number'] : 0.01 * 12 * $post['number']; //总金额
            $deductible_price = $post['is_deductible'] == self::DEDUCTIBLE_ONE ? $discount_price/100 : 0; //抵扣金额
            $deductible_score = $deductible_price * self::SCORE_RATIO; //抵扣所需积分
            //check
            if (($discount_price-$deductible_price) != $post['total_fee']
                || ($deductible_score != 0 && $deductible_score < $current_user_info['score']))
            {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法数据']));
            }
            $member_insert_id = $this->memberAdd($current_user_info['id'],$post); //新增禁用状态会员
            if ($member_insert_id == 0) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法数据']));
            }

            $insert_row['total_fee'] = $discount_price;
            $insert_row['score_payment'] = $deductible_price;
            $insert_row['cash_payment'] = $discount_price - $deductible_price;
            $insert_row['discount_fee'] = $original_price - $discount_price;
            $insert_row['score'] = $deductible_score;
            $insert_row['relation_id'] = $member_insert_id;
        } else if ($post['type'] == self::TYPE_THREE) {
            //历年真题
            if ((!$this->formControlEmpty(['is_deductible'], $post))) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            //获取真题信息
            $exercises_info = $this->db->exercises()->where(['id'=>$post['relation_id'], 'status'=>1])->fetch();
            if (!$exercises_info) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'真题不存在']));
            }
            $exercises_info = iterator_to_array($exercises_info->getIterator());
            if ($exercises_info['test_end'] < time()) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'真题已过期']));
            }
            $discount_price = 0;
            //是否打折
            if ($exercises_info['is_discount'] == ExercisesController::TYPE_ONE && $exercises_info['discount_end'] >= time()) {
                //打折
                $discount_price = $exercises_info['price'] * ($exercises_info['discount']/10);
            }
            //是否抵扣积分
            $deductible_price = $post['is_deductible'] == self::DEDUCTIBLE_ONE ? ($exercises_info['price'] - $discount_price)/100 : 0; //抵扣金额
            $deductible_score = $deductible_price * self::SCORE_RATIO; //抵扣所需积分
            //check
            if (($exercises_info['price'] - $discount_price - $deductible_price) != $post['total_fee']
                || ($deductible_score != 0 && $deductible_score < $current_user_info['score']))
            {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法数据']));
            }
            $insert_row['total_fee'] = $exercises_info['price'];
            $insert_row['score_payment'] = $deductible_price;
            $insert_row['cash_payment'] = $exercises_info['price'] - $discount_price - $deductible_price;
            $insert_row['discount_fee'] = $discount_price;
            $insert_row['score'] = $deductible_score;
            $insert_row['relation_id'] = $post['relation_id'];
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'商品类型错误']));
        }

        //订单号
        $sn = $this->_createSn($current_user_info['id']);
        $insert_row['uid'] = $current_user_info['id'];
        $insert_row['sn'] = $sn;
        $insert_row['create_time'] = time();
        $result = $this->db->order()->insert($insert_row);
        $insert_id = $result ? $this->db->order()->insert_id() : 0;
        $return = $insert_id > 0 ? ['status' => true, 'message' => '操作成功', 'data'=>$insert_id] : ['status' => false, 'message' => '操作失败'];
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
        $order_id = $this->db->order()->where(['uid'=>$cache_user_info['id'],'id'=>$post['id']])->fetch('id');
        if (!$order_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'订单不存在']));
        }

        $update_row = [
            'status' => 3
        ];

        $result = $this->db->order()->where(['id'=>$post['id']])->update($update_row);
        if ($result !== false ) {
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 新增禁用会员 */
    public function memberAdd($uid, $post)
    {
        $str = $post['time_type'] == MemberController::METHOD_ONE ? ' months' : ' years';
        //未过期的会员
        $member_info = $this->iterator_array($this->db->member()->select('')->where(['uid'=>$uid,'expiration_time >= ?'=>time()])->order('expiration_time desc')->limit(1));
        $str = '+ ' . $post['number'] . $str;
        if (empty($member_info)) {
            //新会员
            $expiration_time = strtotime($str);
        } else {
            //续费会员
            $expiration_time = strtotime($str, $member_info[0]['expiration_time']);
        }

        $insert_row = [
            'uid' => $uid,
            'method' => MemberController::METHOD_ONE,
            'time_type' => $post['time_type'],
            'number' => $post['number'],
            'expiration_time' => $expiration_time,
            'status' => 2,
            'create_time' => time()
        ];
        $result = $this->db->member()->insert($insert_row);
        return $result ? $this->db->member()->insert_id() : 0;
    }

    /**
     * 支付成功订单逻辑处理
     * @param $order_info
     * @return bool
     */
    public static function handleOrder($order_info)
    {
        Tools::logger('order','order','start');
        //check success
        if (empty($order_info)) {
            return false;
        }
        Controller::$db_static->transaction = 'BEGIN';
        $user_order_update = Controller::$db_static->order()->where(['id'=>$order_info['id']])->update(['pay_time'=>time(),'state'=>self::STATE_TWO]);
        $user_original_score = Controller::$db_static->user_global()->where(['id'=>$order_info['uid']])->fetch('score');
        if ($order_info['type'] == OrderController::TYPE_ONE) {
            //积分购买
            $score = $order_info['total_fee'] * self::SCORE_RATIO;
            $score_total = bcadd($user_original_score , $score, 2);
            Tools::logger('order','order_score_type_one_total',var_export($score_total, true));
            $user_score_update = Controller::$db_static->user_global()->where(['id'=>$order_info['uid']])->update(['score'=>$score_total]);
            $user_details_insert = Controller::$db_static->score_details()->insert([
                'uid' => $order_info['uid'],
                'order_id' => $order_info['id'],
                'score' => $score,
                'remark' => '购买积分',
                'type' => ScoreDetailsController::TYPE_ONE,
                'create_time' => time()
            ]);
            if ($user_score_update !== false && $user_order_update !== false && $user_details_insert !== false) {
                Controller::$db_static->transaction = 'COMMIT';
                $return = true;
            } else {
                Controller::$db_static->transaction = 'ROLLBACK';
                $return = false;
            }
        } else if ($order_info['type'] == OrderController::TYPE_TWO) {
            //会员办理
            $member_status_update = Controller::$db_static->member()->where(['id'=>$order_info['relation_id']])->update(['status'=>1]);
            //积分抵扣
            $score_total = bcsub($user_original_score , $order_info['score'], 2);
            Tools::logger('order','order_score_type_two_total',var_export($score_total, true));
            $user_score_update = Controller::$db_static->user_global()->where(['id'=>$order_info['uid']])->update(['score'=>$score_total]);
            //details
            $user_details_insert = true;
            if ($order_info['score'] > 0) {
                $user_details_insert = Controller::$db_static->score_details()->insert([
                    'uid' => $order_info['uid'],
                    'order_id' => $order_info['id'],
                    'score' => -$order_info['score'],
                    'remark' => '会员积分抵扣',
                    'type' => ScoreDetailsController::TYPE_TWO,
                    'create_time' => time()
                ]);
            }
            if ($member_status_update !== false && $user_order_update !== false
                && $user_details_insert !== false && $user_score_update !== false
                && $score_total >= 0
            ) {
                Controller::$db_static->transaction = 'COMMIT';
                $return = true;
            } else {
                Controller::$db_static->transaction = 'ROLLBACK';
                $return = false;
            }
        } else if ($order_info['type'] == OrderController::TYPE_THREE) {
            //历年真题购买
            $user_exercises_insert = Controller::$db_static->user_exercises()->insert([
                'uid' => $order_info['uid'],
                'exercises_id' => $order_info['relation_id'],
                'create_time' => time()
            ]);
            //积分抵扣
            $score_total = bcsub($user_original_score , $order_info['score'], 2);
            $user_score_update = Controller::$db_static->user_global()->where(['id'=>$order_info['uid']])->update(['score'=>$score_total]);
            //details
            $user_details_insert = true;
            if ($order_info['score'] > 0) {
                $user_details_insert = Controller::$db_static->score_details()->insert([
                    'uid' => $order_info['uid'],
                    'order_id' => $order_info['id'],
                    'score' => -$order_info['score'],
                    'remark' => '真题积分抵扣',
                    'type' => ScoreDetailsController::TYPE_THREE,
                    'create_time' => time()
                ]);
            }
            if ($user_order_update !== false && $user_details_insert !== false
                && $user_score_update !== false && $user_exercises_insert !== false
                && $score_total >= 0
            ) {
                Controller::$db_static->transaction = 'COMMIT';
                $return = true;
            } else {
                Controller::$db_static->transaction = 'ROLLBACK';
                $return = false;
            }
        } else {
            return false;
        }
        return $return;
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
        $order_info = $this->db->order()->where(['id' => $get['id'], 'uid' => $cache_user_info['id']])->fetch();
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

    /* 订单号生成 */
    private function _createSn($uid)
    {
        $rand_number = mt_rand(100000, 999999);
        $sn = date('YmdHis', time()) . $uid . $rand_number;
        $id = $this->db->order()->where(['sn'=>$sn])->fetch('id');
        if ($id) {
            return $this->_createSn($uid);
        } else {
            return $sn;
        }
    }

    /* 订单列表 */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!empty($get['state']) && !in_array($get['state'], [1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'参数错误']));
        }

        $search = [
            'uid' => $cache_user_info['id'],
            'state' => empty($get['state']) == 1 ?  '' : $get['state'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
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