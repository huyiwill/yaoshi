<?php

namespace front\library;

require_once __DIR__ . "/../../vendor/wxpay/lib/WxPay.Notify.php";
require_once __DIR__ . "/../../vendor/wxpay/lib/WxPay.Api.php";
use front\api\OrderController;
use \Psr\Container\ContainerInterface;

class WxNotify extends \WxPayNotify
{
    private $db;

    public function __construct(ContainerInterface $ci)
    {
        $this->db = $ci->get('db');
    }

    public function NotifyProcess($data, &$msg)
    {
        //用户基础该类之后需要重写该方法，成功的时候返回true，失败返回false
        if (!$data) {
            //check failure
            return false;
        }
        //check success
        $order_info = $this->db->order()->where(['sn'=>$data['out_trade_no']])->fetch();
        if (!$order_info) {
            return false;
        }

        $order_info = iterator_to_array($order_info->getIterator());
        if (($order_info['cash_payment'] * 100) != $data['total_fee'] || \WxPayConfig::APPID != $data['appid']
            || \WxPayConfig::MCHID != $data['mch_id'] || $order_info['state'] != OrderController::STATE_ONE
        ) {
            return false;
        }

        //业务逻辑
        $order_result = OrderController::handleOrder($order_info);
        if (!$order_result) {
            return false;
        }
        return true;
    }
}