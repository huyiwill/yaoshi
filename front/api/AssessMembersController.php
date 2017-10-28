<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class AssessMembersController
 * @package front\api
 */
class AssessMembersController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    public function actionSearch(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['search'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $user_info = $this->db->user_global()->or('mobile=:mobile or name=:name or nickname=:nickname',[
            ':mobile' => $get['search'], ':name' => $get['search'], ':nickname' => $get['search']
        ])->fetch();
        if ($user_info) {
            $user_info = iterator_to_array($user_info->getIterator());
            unset($user_info['password']);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $user_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "用户不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['uid'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_members_id = $this->db->assess_members()->where(['uid' => $post['id'], 'uid_admin' => $cache_user_info['id'],'status'=>[1,2]])->fetch('id');
        if ($assess_members_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'成员已存在']));
        }
        $insert_row = [
            'uid' => $post['uid'],
            'uid_admin' => $cache_user_info['id'],
            'create_time' => time()
        ];
        $result = $this->db->assess_members()->insert($insert_row);
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

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $update_row = [
            'status' => 3
        ];
        $result = $this->db->assess_members()->where(['id'=>$post['id'],'uid_admin' => $cache_user_info['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','members_group_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_members_id = $this->db->assess_members()->where(['id' => $post['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch('id');
        if (empty($assess_members_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'成员不存在']));
        }
        $update_row = [
            'members_group_id' => $post['members_group_id'],
        ];
        $result = $this->db->assess_members()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* list */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid_admin' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->assess_members()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->assess_members()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function(&$item) {
                $item['members_group_name'] = $this->db->assess_members_group()->where(['id'=>$item['members_group_id']])->fetch('name');
                //成员详情
                $user_global_info = $this->db->user_global()->where(['id' => $item['uid']])->fetch();
                $user_global_info = $user_global_info ? iterator_to_array($user_global_info->getIterator()) : [];
                unset($user_global_info['password']);
                $item['user_info'] = $user_global_info;
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
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_members_info = $this->db->assess_members()->where(['id' => $get['id'], 'uid_admin' => $cache_user_info['id'], 'status'=>[1]])->fetch();
        if ($assess_members_info) {
            $assess_members_info = iterator_to_array($assess_members_info->getIterator());
            $assess_members_info['members_group_name'] = $this->db->assess_members_group()->where(['id'=>$assess_members_info['members_group_id']])->fetch('name');
            //成员详情
            $user_global_info = $this->db->user_global()->where(['id' => $assess_members_info['uid']])->fetch();
            $user_global_info = $user_global_info ? iterator_to_array($user_global_info->getIterator()) : [];
            unset($user_global_info['password']);
            $assess_members_info['user_info'] = $user_global_info;
            $return = [
                'status' => true,
                'message' => '',
                'data' => $assess_members_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "成员不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionDrop(Request $request, Response $response)
    {
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        //group
        $group_list = $this->iterator_array($this->db->assess_members_group()->where(['uid_admin' => $cache_user_info['id'],'status'=>1])->order('id asc')->select('id,name'));
        if (empty($group_list)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不存在成员分组']));
        }
        $result = [];
        foreach ($group_list as $key => $item) {
            $result[$key]['id'] = $item['id'];
            $result[$key]['name'] = $item['name'];
            $members = $this->iterator_array($this->db->assess_members()->where(['uid_admin' => $cache_user_info['id'],'members_group_id' => $item['id'],'status'=>1])->order('id asc')->select('uid'));
            if (!empty($members)) {
                array_walk($members, function (&$item) {
                    $item['name'] = $this->db->user_global()->where(['id'=>$item['uid']])->fetch('name');
                });
            }
            $result[$key]['members'] = $members;
        }
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