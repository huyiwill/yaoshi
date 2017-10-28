<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class MemberController
 * @package backend\api
 */
class MemberController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* add 生成会员卡 */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['type_id','number'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $member_card_type_info = $this->db->member_card_type()->where(['id' => $post['type_id'], 'status'=>1])->fetch();
        if (empty($member_card_type_info)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'卡类型不存在']));
        }

        $cache_user_info = $this->getLogin('_ys_backend_login', $request);
        $insert_rows = [];
        for ($i=0; $i<(int)$post['number']; $i++) {
            $number = $this->_createCardNumber();
            $insert_rows[] = [
                'admin_id' => $cache_user_info['id'],
                'type_id' => $post['type_id'],
                'card_number' => $number,
                'create_time' => time()
            ];
        }
        $result = $this->db->member_card()->insert_multi($insert_rows);
        $return = $result !== false ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 生成会员卡号 */
    private function _createCardNumber()
    {
        $number = mt_rand(100000000000,999999999999);
        $member_card_id = $this->db->member_card()->where(['card_number'=>$number,'status'=>[1,2]])->fetch('id');
        if (!empty($member_card_id)) {
            return $this->_createCardNumber();
        }
        return $number;
    }

    /* 禁用/启用 */
    public function actionStatus(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'status'], $post) || !in_array($post['status'], [1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $member_card_id = $this->db->member_card()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($member_card_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'会员卡不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->member_card()->where(['id'=>$post['id']])->update($update_row);
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
        $result = $this->db->member_card()->where(['id'=>$post['id']])->update($update_row);
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
        $count = $this->db->member_card()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->member_card()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function (&$item) {
                //type_info
                $type_info = $this->db->member_card_type()->where(['id'=>$item['type_id']])->fetch();
                $item['type_info'] = $type_info ? iterator_to_array($type_info->getIterator()) : [];
                $item['admin_name'] = $this->db->admin()->where(['id'=>$item['admin_id']])->fetch('nickname');
            });
        }
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

        $member_card_info = $this->db->member_card()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($member_card_info) {
            $member_card_info = iterator_to_array($member_card_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $member_card_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "会员卡不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->member_card()->where(['status'=>1])->order('id asc')->select('id,card_number'));
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