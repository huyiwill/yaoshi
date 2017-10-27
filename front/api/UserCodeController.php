<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class UserCodeController
 * @package front\api
 */
class UserCodeController extends Controller
{
    const CODE_SCORE = 100; //邀请码获得分值

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* 填写邀请码 */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['code'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        //check
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $user_code_id = $this->db->user_code()->where(['`to`'=>$cache_user_info['id']])->fetch('id');
        if ($user_code_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不可重复填写']));
        }

        $user_global_info = $this->db->user_global()->where(['code'=>$post['code'], 'status'=>1])->fetch();
        if (!$user_global_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'邀请码不可用']));
        }
        $user_global_info = iterator_to_array($user_global_info->getIterator());

        $insert_row = [
            '`from`' => $user_global_info['id'],
            '`to`' => $cache_user_info['id'],
            'code' => $post['code'],
            'score' => self::CODE_SCORE,
            'create_time' => time()
        ];

        $this->db->transaction = 'BEGIN';
        $result = $this->db->user_code()->insert($insert_row);
        $result_score_details_insert = $this->db->score_details()->insert([
            'uid' => $user_global_info['id'],
            'score' => self::CODE_SCORE,
            'remark' => '邀请码',
            'type' => ScoreDetailsController::TYPE_FOUR,
            'create_time' => time()
        ]);
        $score = $user_global_info['score'] + self::CODE_SCORE;
        $result_user_global_update = $this->db->user_global()->where(['id'=>$user_global_info['id']])->update(['score'=>$score]);
        if ($result !== false && $result_score_details_insert !== false
            && $result_user_global_update !== false
        ) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 审核题目列表 */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            '`from`' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->user_code()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->user_code()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function (&$item) {
                $username = $this->db->user_global()->where(['id'=>$item['to']])->fetch('name');
                $item['to_name'] = $username ? $username : '';
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
}