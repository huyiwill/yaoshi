<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class AssessUserController
 * @package front\api
 */
class AssessUserController extends Controller
{
    const STATE_ONE = 1; //未考核
    const STATE_TWO = 2; //批阅中
    const STATE_THREE = 3; //已批阅
    const STATE_FOUR = 4; //已发布
    const STATE_FIVE = 5; //已结束

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* 修改成绩 */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'other_score'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $user_assess_info = $this->db->user_assess()->where(['id' => $post['id'], 'status'=>[1]])->fetch();
        if (!$user_assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'用户考核不存在']));
        }
        $user_assess_info = iterator_to_array($user_assess_info->getIterator());
        $assess_info = $this->db->assess()->where(['id' => $user_assess_info['assess_id'], 'uid_admin' => $cache_user_info['id'], 'status'=>1])->fetch();
        if (!$assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }

        $update_row = [
            'other_score' => $post['other_score']
        ];
        $result = $this->db->user_assess()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 批阅 */
    public function actionMaking(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'test_subject','four_score','five_score','six_score'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $user_assess_info = $this->db->user_assess()->where(['id' => $post['id'], 'status'=>[1]])->fetch();
        if (!$user_assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'用户考核不存在']));
        }
        $user_assess_info = iterator_to_array($user_assess_info->getIterator());
        $assess_info = $this->db->assess()->where(['id' => $user_assess_info['assess_id'], 'uid_admin' => $cache_user_info['id'], 'status'=>1])->fetch();
        if (!$assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }

        $update_row = [
            'test_subject' => json_encode($post['test_subject']),
            'practice_score' => $post['four_score'] + $post['five_score'] + $post['six_score'],
            'four_score' => $post['four_score'],
            'five_score' => $post['five_score'],
            'six_score' => $post['six_score'],
            'state' => self::STATE_THREE
        ];
        $result = $this->db->user_assess()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 发布 */
    public function actionState(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['ids'], $post) || !is_array($post['ids'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $update_row = [
            'state' => self::STATE_FOUR,
        ];
        $i = 0;
        foreach ($post['ids'] as $key=>$item) {
            $user_assess_info = $this->db->user_assess()->where(['id' => $item])->fetch();
            if (!$user_assess_info) {
                continue ;
            }
            $user_assess_info = iterator_to_array($user_assess_info->getIterator());
            if ($user_assess_info['state'] != self::STATE_THREE) {
                continue ;
            }
            $result = $this->db->user_assess()->where(['id'=>$item])->update($update_row);
            !$result ? : $i++;
        }
        $return = ['status' => true, 'message' => "成功发布{$i}个人的成绩"];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* list */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        if (!$this->formControlEmpty(['assess_id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        //check
        //$assess_info = $this->db->assess()->where(['id' => $get['assess_id'], 'uid_admin' => $cache_user_info['id'], 'status'=>1])->fetch();
        //if (!$assess_info) {
        //    return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        //}
        $search = [
            'assess_id' => $get['assess_id'],
            'uid' => @$get['uid'] ? $get['uid'] : '',
        ];
        if (!empty($get['state'] && in_array($get['state'], [1,2,3,4]))) {
            $search['state'] = $get['state'];
        }
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->user_assess()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->user_assess()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function(&$item) {
                $item['name'] = $this->db->user_global()->where(['id' => $item['uid']])->fetch('name');
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
        $user_assess_info = $this->db->user_assess()->where(['id' => $get['id'], 'status'=>[1]])->fetch();
        if ($user_assess_info) {
            $user_assess_info = iterator_to_array($user_assess_info->getIterator());
            $user_assess_info['test_subject'] = json_decode($user_assess_info['test_subject'], true);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $user_assess_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "考核不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}