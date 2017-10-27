<?php

namespace front\api;

require_once __DIR__ . "/../../vendor/alipay/pagepay/service/AlipayTradeService.php";
require_once __DIR__ . "/../../vendor/wxpay/lib/WxPay.Api.php";

use front\library\Tools;
use front\library\WxNotify;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;

/**
 * Class PayController
 * @package front\api
 */
class PayController extends Controller
{
    const PAY_METHOD_ONE = 1; //支付宝
    const PAY_METHOD_TWO = 2; //微信

    private $config = [
        'alipay' => [
            //应用ID,您的APPID。
            'app_id' => "2017021705714183",
            'seller_id' => "2088421926558592",
            //商户私钥
            'merchant_private_key' => "MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQC0UZddBUMRurE4VpYlC26ZToF5YZeayAWhkl1324RaLomnP6eYFRX7xRzORrysa9eX4fzCivzCjdh62N0bkQTuT6KOrEk8221KCBGIe1JwzRCnrkhJe8K6+7tTRfZ0j/S8tb5O4frkgdNa0QoWtwSoWaZhrAnEnGGnKfJDO/AzBVG0HS9IqTl7PmPgHPGIJYoYMCoDDT6GNDMsnhriW2sKmbTUMitYFNzJykiOyXrW/T/3kmTece2U67iy4mYDfbJ6hq9hphjJjoK+ZMnyM966G78HBxgHXYmZqTrQaHsYfNIIKI+a6MgE1tO+lzp116tMBaqHMVYiKVhqu4I3+fF7AgMBAAECggEAQtFdMnYJtV8l3oePWajT0fFaXv0/7XYjSAzxn8FzWOuXzKb01pExtTsEgpeJwKaxnDF8tSZFpV5kkpkbzeQ+HITb6tyCww9Yfy9gN9/i27PRcSFO3hQDQ8CWWLQ7MFRLz0XVgGuSyewlwqhlgadODYgy9EyIzhdYxFXx1OI4Dt9Re5oWzptkid6xxg01ZnB/IvW2cVNBwFO/OFsjcoAb5m3jb1w7XIC950nOMVUg4w5gyL3oFCCbe7bQandEsTYVH+h1VWRiGgiGWDqx2VznkYbc2zCvH7xXyTTTD7wlCn6ga5WW6Yq7CV313OxGTR0jpa83N5gewnp7Jjf4iTNkqQKBgQDYuAY2JbffaFy/sqyEMc62eW6ABhTsQfc2wEX330FbMPtCVVGUYDYku6Qc3LidMXg6mxbnTjUlbEaMVuygcS37QtCSdd76SIwty3gITxa6zGMksQR7yz8Tk/H6amHJuw+qV5se1TEqeUY8AW7NiSbAd69pPpjBTX2D7ZWyctjmJwKBgQDVAJBX8yf3O+gLM75ACJ4tBzlmiGUWbUrEvfPjqV3nF0ylkQXQjHN/Sf8Ga18MuQ+MXWRoEaiYAHqgYDjd3VZn72lmaEPkviSj8Fovzj3coclGhSKvSheSalE9H/G1BB9Ws9SvaJDejbG44e+KkCnvo2I7LpuSSLAyuO1dUb4ijQKBgQDDWM2wjTs609WWrjW3SzYrYTXrjI/bjnKKFbJ8Be/hhTtWxZUti7QCVGlP4t0+RFM9cIKqqOJbA0hsRAYzcRGjhMyA2GLPdAl3VgqyYqf4ZTeQ4UdLVx9aRGc/9BiFQGqUfzdCCaWxxXM7r4bzGgemQbwJv/TteS4Ed2oSWleMzQKBgQCyFG6ZmWlogmVWOERvNMVJ0ChlWNNrFKTWKBmn1Qn1Er26Dq1V1pcZbLwSmeI1uOMO72XqvBjAPGZJfykMFOkQrFDqlXFt5KqthV1haoS92WV12AlPlBpxBwI0oGhsdq3cxyyiGkJETyKu9/ZVWoI1H9wYrrbSboMhZQrOYnEcYQKBgFnqiG39OogKts6WJA9tuXrts9EFBSiDlI+KucyNZoqfQKw/xXTXxFsuLrEULCH4nWZ66CJHgcFAqZ3mS1srR+KdnWF6zVh/W2NaOz/tDjKcPiQkKCm+ioCWBB0j8GIM3iFKo0bD3iaTGu3IIy4ANZx1paV8+bPfPpZWIQp98CWK",
            //异步通知地址
            //'notify_url' => "http://zhsy_home.shnow.cn/notify",
            //同步跳转
            //'return_url' => "http://zhsy_home.shnow.cn/me.html?type=score",
            //编码格式
            'charset' => "UTF-8",
            //签名方式
            'sign_type'=>"RSA2",
            //支付宝网关
            'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
            //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
            'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEArx6VxrJ0md5kXQ6ZtMf9PPSaQj3Pgaw5+9eY1wT954FmiXlvEK688bTEvVq8/lw6kakqrXSEzqLg7VcNE93bvfNTBK12BnRD16pVCg4k3E140+cfbb7SvQyWal62VdSnzoIjEH1MLU16kuW0mc2QS76HTdM2/MiMW/23iI1drYklWkJvwDZwc5PbJB/973xJZptSk5BME8amSOBVf8u4bszYZytf/YRsW3w/U7vA7LB9LxL24A/k59L9V+Wm3iAuBgabgC0yixSFnn22E+xDPpoSYgd+EzGNZ6TKwzy4aYDFS9YdL9PexG3UUpswA4eHZICeVZlVljsT6kTg2S1gNwIDAQAB"
        ]
    ];

    /* pay */
    public function actionPay(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty  订单id
        if ((!$this->formControlEmpty(['order_id','pay_method'], $post)) || !in_array($post['pay_method'],[1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $order_info = $this->db->order()->where(['id'=>$post['order_id']])->fetch();
        if (!$order_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'订单不存在']));
        }
        $order_info = iterator_to_array($order_info->getIterator());
        if ($post['pay_method'] == self::PAY_METHOD_ONE) {
            //支付宝
            $this->config['alipay']['return_url'] = $post['return_url'] ? $post['return_url'] : '';
            $html = $this->_aLiPay($order_info);
            if ($html == false) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'请求失败']));
            } else {
                $this->db->order()->where(['id'=>$order_info['id']])->update(['pay_method'=>self::PAY_METHOD_ONE]);
                return $response->withHeader('Content-type', 'application/html')->write($html);
            }
        } else {
            //微信
            $wechat_result = $this->_wechat($order_info);
            if ($wechat_result['status'] == false) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>$wechat_result['msg']]));
            }
            $this->db->order()->where(['id'=>$order_info['id']])->update(['pay_method'=>self::PAY_METHOD_TWO]);
            $url = urlencode($wechat_result['url']);
            $url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST']. "/qr/code/{$url}.json";
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>true,'data'=>['url'=>$url]]));
        }
    }

    /* 微信扫码回调 */
    public function actionWxNotify()
    {
        //check
        $notify = new WxNotify($this->ci);
        $notify->Handle(false);
        Tools::logger('wx','pay_notify_wx','handle end');
    }

    /* 支付宝回调 */
    public function actionNotify()
    {
        $notify_data = $_POST;
        Tools::logger('alipay','pay_notify_ali_data',var_export($notify_data,true));
        //check
        $aop = new \AlipayTradeService($this->config['alipay']);
        $check = $aop->check($notify_data);
        if(!$check) {
            Tools::logger('alipay','pay_notify_ali_check','check sign failure');
            //验证失败
            echo "fail";
            exit;
        }
        Tools::logger('alipay','pay_notify_ali_check','check sign success');
        //订单是否存在 && 未支付状态
        $order_info = $this->db->order()->where(['sn'=>$notify_data['out_trade_no']])->fetch();
        if (!$order_info) {
            echo 'fail';
            exit;
        }
        $order_info = iterator_to_array($order_info->getIterator());
        Tools::logger('alipay','pay_notify_ali_data',var_export($order_info,true));
        if ($order_info['cash_payment'] != $notify_data['total_amount']
            || $this->config['alipay']['app_id'] != $notify_data['app_id']
            || $this->config['alipay']['seller_id'] != $notify_data['seller_id']
        ) {
            echo 'fail';
            exit;
        }

        if($_POST['trade_status'] == 'TRADE_FINISHED') {
            //停止退款状态不需要
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
            //如果有做过处理，不执行商户的业务程序

            //注意：
            //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
            //判断该笔订单是否在商户网站中已经做过处理
            //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
            //请务必判断请求时的total_amount与通知时获取的total_fee为一致的
            //如果有做过处理，不执行商户的业务程序
            //注意：
            //付款完成后，支付宝系统发送该交易状态通知
            if ($order_info['state'] != OrderController::STATE_ONE) {
                echo 'fail';
                exit;
            }
            //业务逻辑
            $order_result = OrderController::handleOrder($order_info);
            if (!$order_result) {
                echo 'fail';
                exit;
            }
        }
        Tools::logger('alipay','pay_notify_ali','true end');
        echo "success";	//请不要修改或删除
        exit;
    }

    private function _aLiPay($orderInfo)
    {
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $orderInfo['sn'];
        //订单名称，必填
        $subject = trim($orderInfo['product_subject']);
        //付款金额，必填
        $total_amount = trim($orderInfo['cash_payment']);
        //商品描述，可空
        $body = trim($orderInfo['product_body']);

        //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($this->config['alipay']);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $notify_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . '/notify';
        $response = $aop->pagePay($payRequestBuilder,$this->config['alipay']['return_url'],$notify_url);
        return $response;
    }

    private function _wechat($orderInfo)
    {
        //统一下单api
        $notify_url = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] . '/wxNotify';
        //WxPayUnifiedOrder
        $notify = new \NativePay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($orderInfo['product_body']);
        $input->SetOut_trade_no($orderInfo['sn']);
        $input->SetTotal_fee($orderInfo['cash_payment'] * 100);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetNotify_url($notify_url);
        $input->SetTrade_type("NATIVE");
        $input->SetProduct_id($orderInfo['type']);
        $result = $notify->GetPayUrl($input);
        if ($result['result_code'] == 'SUCCESS' && $result['return_code'] == 'SUCCESS') {
            return [
                'status' => true,
                'url' => $result['code_url']
            ];
        }
        return [
            'status' => false,
            'msg' => $result['return_msg'] ? $result['return_msg'] : '获取失败'
        ];
    }
}