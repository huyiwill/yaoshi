<?php

namespace front\api;

use front\library\Tools;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class CategoryController
 * @package front\api
 */
class CategoryController extends Controller
{
    private $_limit = 10;

    public function actionSpecial(Request $request, Response $response)
    {
        //专项练习
        //职业下所有分类
        $current_user_info = $this->currentUserInfo('_ys_front_login', $request);
        if ($current_user_info['role'] == UserController::ROLE_TWO) {
            //药师根据职务
            $post = $this->db->user_pharmacist()->where(['uid'=>$current_user_info['id']])->fetch('post');
            $result = $this->iterator_array($this->db->subject()->select('pharmacy_id, COUNT(1) total')->where(['`role`'=>$current_user_info['role'],'`post`'=>$post,'topic_type'=>[1,2], 'status'=>1])->group('pharmacy_id')->order('pharmacy_id asc'));
        } else {
            //其他根据职业
            $result = $this->iterator_array($this->db->subject()->select('pharmacy_id, COUNT(1) total')->where(['`role`'=>$current_user_info['role'],'topic_type'=>[1,2], 'status'=>1])->group('pharmacy_id')->order('pharmacy_id asc'));
        }

        if (!empty($result)) {
            array_walk($result, function (&$item) {
                $item['category_name'] = $this->db->pharmacy_category()->where(['id'=>$item['pharmacy_id']])->fetch('name');
            });
            $return = [
                'status' => true,
                'data' => $result
            ];
        } else {
            $return = [
                'status' => true,
                'data' => []
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionOptions(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->pharmacy_category()->where(['status'=>1])->order('id asc')->select('id,pid,name'));
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