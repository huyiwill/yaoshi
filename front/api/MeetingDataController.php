<?php
/**
 * 会议资料模块
 */
namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class MeetingController
 * @package front\api
 */
class MeetingDataController extends Controller{
    const DETAIL_TYPE_ONE   = 1; //会议介绍
    const DETAIL_TYPE_TWO   = 2; //会议日程
    const DETAIL_TYPE_THREE = 3; //会议嘉宾
    const DETAIL_TYPE_FOUR  = 4; //会议邀请函

    const EXAMINE_TYPE_ONE   = 1; //待审核
    const EXAMINE_TYPE_TWO   = 2; //审核通过
    const EXAMINE_TYPE_THREE = 3; //审核未通过

    private $_limit        = 10;
    private $_search_black = [
        'page',
        'order'
    ];

    /**
     *会议资料列表
     */
    public function actionMeetingDataList(Request $request, Response $response){
        $get             = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search          = [
            'uid_admin' => $cache_user_info['id'],
            'name'      => @$get['soso_search_name'] ? $get['soso_search_name'] : ''
        ];
        $limit           = $this->_limit;
        $page            = !empty($get['page']) ? $get['page'] : 1;
        $search          = $this->_commonSearch($search, $this->_search_black);
        $order           = !empty($get['order']) ? $get['order'] : 'id desc';
        try{
            $count     = $this->db->meeting()->select('')->where($search)->count();
            $number    = ceil($count / $limit);
            $result    = $this->db->meeting()->select('id', 'name', 'data', 'status')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
            $data      = $this->iterator_array($result);
            $meet_data = array();
            foreach($data as $k => $v){
                if(!empty($v['data'])){
                    $v['data'] = json_decode($v['data'], true);
                    foreach($v['data'] as $k1 => $v1){
                        if(empty($v1['realPath']) || !isset($v1['realPath'])){
                            $v['data'][$k1]['data_status'] = 0;  //表示未上传资料
                        }else{
                            $v['data'][$k1]['data_status'] = 1;  //表示已上传资料
                        }
                        $v['data'][$k1]['jin']  = (!isset($v1['jin'])) ? 0 : $v1['jin'];  //是否被禁用
                        $v['data'][$k1]['id']   = $v['id'];
                        $v['data'][$k1]['name'] = $v['name'];
                        $meet_data[]            = $v['data'][$k1];
                    }
                }else{
                    unset($data[$k]);
                }
            }
            $return = [
                'status'  => true,
                'current' => $page,
                'total'   => $number,
                'data'    => $meet_data,
            ];
            return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
        }
        catch(\Exception $e){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '无会议资料数据'
            ]));
        }
    }

    /**
     * 添加会议资料
     */
    public function actionAddMeetData(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty([
            'name',
            'contacts',
            'mobile',
            'enroll_start',
            'enroll_end',
            'time_start',
            'time_end',
            'attend_time',
            'province_id',
            'city_id',
            'address',
            'venue_name',
            'is_credit',
            'credis',
            'type',
            'subject'
        ], $post)
        ){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_id      = $this->db->meeting()->where([
            'name'      => $post['name'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => [1, 2]
        ])->fetch('id');
        if($meeting_id){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议名称已存在'
            ]));
        }
        $insert_row = [
            'uid_admin'    => $cache_user_info['id'],
            'contacts'     => $post['contacts'],
            'mobile'       => $post['mobile'],
            'name'         => $post['name'],
            'name_english' => $post['name_english'] ? $post['name_english'] : '',
            'enroll_start' => strtotime($post['enroll_start']),
            'enroll_end'   => strtotime($post['enroll_end']),
            'time_start'   => strtotime($post['time_start']),
            'time_end'     => strtotime($post['time_end']),
            'attend_time'  => strtotime($post['attend_time']),
            'province_id'  => $post['province_id'],
            'city_id'      => $post['city_id'],
            'address'      => $post['address'],
            'venue_name'   => $post['venue_name'],
            'is_credit'    => $post['is_credit'],
            'credis'       => $post['credis'],
            'type'         => $post['type'],
            'subject'      => $post['subject'],
            '`from`'       => 1,
            'create_time'  => time()
        ];
        if(!empty($post['banner'])){
            //banner处理
            $uploadBanner = $this->uploadBase64Image($post['banner'], '', 'meeting/banner', 'banner');
            if(!empty($uploadBanner)){
                //上传新图片
                $insert_row['real_banner'] = $uploadBanner['realPath'];
                $insert_row['banner']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadBanner['photo'];
            }
        }
        if(!empty($post['icon'])){
            //icon处理
            $uploadIcon = $this->uploadBase64Image($post['icon'], '', 'meeting/icon', 'icon');
            if(!empty($uploadIcon)){
                //上传新图片
                $insert_row['real_icon'] = $uploadIcon['realPath'];
                $insert_row['icon']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadIcon['photo'];
            }
        }

        $result    = $this->db->meeting()->insert($insert_row);
        $insert_id = $this->db->meeting()->insert_id();
        $return    = $result
            ? ['status' => true, 'message' => '操作成功', 'data' => $insert_id]
            : [
                'status'  => false,
                'message' => '操作失败'
            ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * 会议资料禁用
     */
    public function actionMeetDatajin(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id', 'index'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search          = [
            'uid_admin' => $cache_user_info['id'],
            'id'        => @$post['id'] ? $post['id'] : 0
        ];
        $meet_data       = $this->db->meeting()->where($search)->fetch();
        if(!$meet_data){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议资料不存在'
            ]));
        }
        //jin
        $result     = $this->db->meeting()->select('id,data')->where($search);
        $meet_data  = $this->iterator_array($result);
        $meet_data  = $meet_data[0];
        $data       = json_decode($meet_data['data'], true);
        $one        = $data[$post['index']];
        $one['jin'] = 1;
        //反转
        $data[$post['index']] = $one;
        $update_row           = array(
            'data' => json_encode($data)
        );
        $res                  = $this->db->meeting()->where($search)->update($update_row);
        $return               = $res
            ? ['status' => true, 'message' => '操作成功']
            : [
                'status'  => false,
                'message' => '操作失败'
            ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     * 会议资料删除
     */
    public function actionDel(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search          = [
            'uid_admin' => $cache_user_info['id'],
            'id'        => @$post['id'] ? $post['id'] : 0
        ];
        $meeting_info    = $this->db->meeting()->where($search)->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }

        //del
        $result    = $this->db->meeting()->select('id,data')->where($search);
        $meet_data = $this->iterator_array($result);
        $meet_data = $meet_data[0];
        $data      = json_decode($meet_data['data'], true);
        //反转
        if(count($data) > 1){
            //$data[$post['index']] = '';
            unset($data[$post['index']]);
            $update_row = array(
                'data' => json_encode($data)
            );
        }else{
            $update_row = array(
                'data' => ''
            );
        }

        $res    = $this->db->meeting()->where($search)->update($update_row);
        $return = $res
            ? ['status' => true, 'message' => '操作成功']
            : [
                'status'  => false,
                'message' => '操作失败'
            ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /**
     *会议资料上传
     */
    public function actionUploadFile(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search          = [
            'id'        => $post['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ];
        $meeting_id      = $this->db->meeting()->where($search)->fetch('id');
        if(empty($meeting_id)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }
        $result = $this->uploadFiles($_FILES, '', 'meeting/data', 'data');
        if(!empty($result)){
            $res       = $this->db->meeting()->select('id,data')->where($search);
            $meet_data = $this->iterator_array($res);
            if(empty($meet_data) || !$meet_data){
                $data_update = $this->db->meeting()->where(['id' => $post['id']])->update(['data' => json_encode($result)]);
            }else{
                $meet_data = $meet_data[0];
                $data      = json_decode($meet_data['data'], true);
                foreach($result as $v){
                    array_push($data, $v);
                }
                $result      = $data;
                $data_update = $this->db->meeting()->where(['id' => $post['id']])->update(['data' => json_encode($result)]);
            }
            if($data_update !== false){
                $return = ['status' => true, 'message' => '操作成功'];
            }else{
                $return = ['status' => false, 'message' => '操作失败'];
            }
        }else{
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 详细信息添加 */
    public function actionDetailAdd(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['meeting_id', 'sort', 'type', 'state'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        //$meeting_details_id = $this->db->meeting_details()->where(['meeting_id' => $post['meeting_id'], 'sort' => $post['sort'], 'status'=>1])->fetch('id');
        //if (!empty($meeting_details_id)) {
        //    return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'排序已存在']));
        //}
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['meeting_id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }

        if($post['type'] == self::DETAIL_TYPE_ONE){
            //会议介绍
            //form control empty
            if(!$this->formControlEmpty(['content'], $post)){
                return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                    'status'  => false,
                    'message' => '必填参数错误'
                ]));
            }
            $insert_row = [
                'meeting_id'  => $post['meeting_id'],
                'content'     => $post['content'],
                'sort'        => $post['sort'],
                'type'        => $post['type'],
                'create_time' => time(),
            ];
            $result     = $this->db->meeting_details()->insert($insert_row);
            $return     = $result ? true : false;
        }else{
            if($post['type'] == self::DETAIL_TYPE_TWO){
                //会议日程
                //日程添加 schedule
                if(!$this->formControlEmpty(['schedule'], $post) || !is_array($post['schedule'])){
                    return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                        'status'  => false,
                        'message' => '必填参数错误'
                    ]));
                }
                $this->db->transaction = 'BEGIN';
                $schedule_result       = $this->_insert_schedule($post);
                $insert_row            = [
                    'meeting_id'  => $post['meeting_id'],
                    'sort'        => $post['sort'],
                    'type'        => $post['type'],
                    'create_time' => time(),
                ];
                $result                = $this->db->meeting_details()->insert($insert_row);
                if($result !== false && $schedule_result !== false){
                    $this->db->transaction = 'COMMIT';
                    $return                = true;
                }else{
                    $this->db->transaction = 'ROLLBACK';
                    $return                = false;
                }
            }else{
                if($post['type'] == self::DETAIL_TYPE_THREE){
                    //会议嘉宾
                    //嘉宾添加
                    if(!$this->formControlEmpty(['guest'], $post) || !is_array($post['guest'])){
                        return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                            'status'  => false,
                            'message' => '必填参数错误'
                        ]));
                    }
                    $this->db->transaction = 'BEGIN';
                    $guest_result          = $this->_insert_guest($post);
                    $insert_row            = [
                        'meeting_id'  => $post['meeting_id'],
                        'sort'        => $post['sort'],
                        'type'        => $post['type'],
                        'create_time' => time(),
                    ];
                    $result                = $this->db->meeting_details()->insert($insert_row);
                    if($result !== false && $guest_result !== false){
                        $this->db->transaction = 'COMMIT';
                        $return                = true;
                    }else{
                        $this->db->transaction = 'ROLLBACK';
                        $return                = false;
                    }
                }else{
                    if($post['type'] == self::DETAIL_TYPE_FOUR){
                        //会议邀请函
                        //form control empty
                        if(!$this->formControlEmpty(['photo'], $post)){
                            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                                'status'  => false,
                                'message' => '必填参数错误'
                            ]));
                        }
                        $insert_row = [
                            'meeting_id'  => $post['meeting_id'],
                            'sort'        => $post['sort'],
                            'type'        => $post['type'],
                            'create_time' => time(),
                        ];
                        //邀请函处理
                        $uploadInvitation = $this->uploadBase64Image($post['photo'], '', 'meeting/detail/invitation', 'detail.invitation');
                        if(!empty($uploadInvitation)){
                            //上传新图片
                            $insert_row['real_photo'] = $uploadInvitation['realPath'];
                            $insert_row['photo']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadInvitation['photo'];
                        }
                        $result = $this->db->meeting_details()->insert($insert_row);
                        $return = $result ? true : false;
                    }else{
                        return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                            'status'  => false,
                            'message' => '参数错误'
                        ]));
                    }
                }
            }
        }

        $return = $return ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* detail info */
    public function actionDetailInfo(Request $request, Response $response){
        $get = $request->getQueryParams();
        //form control empty
        if(!$this->formControlEmpty(['meeting_id', 'type'], $get) || !in_array($get['type'], [2, 3])){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $get['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }

        if($get['type'] == self::DETAIL_TYPE_TWO){
            //日程
            $return = $this->iterator_array($this->db->meeting_schedule()->select('')->where(['meeting_id' => $get['meeting_id']])->order('days asc'));
        }else{
            //嘉宾
            $return = $this->iterator_array($this->db->meeting_guest()->select('')->where(['meeting_id' => $get['meeting_id']])->order('create_time asc'));
        }
        $return = [
            'status'  => true,
            'message' => '',
            'data'    => $return
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* detail update */
    public function actionDetailUpdate(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id', 'meeting_id', 'sort', 'state'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $meeting_details_id = $this->db->meeting_details()->where([
            'meeting_id' => $post['meeting_id'],
            'sort'       => $post['sort'],
            'Not id'     => $post['id'],
            'status'     => [1, 2]
        ])->fetch('id');
        if(!empty($meeting_details_id)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '排序已存在'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['meeting_id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }

        //detail info
        $meeting_details_info = $this->db->meeting_details()->where(['id' => $post['id'], 'status' => 1])->fetch();
        if(!$meeting_details_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议详细信息不存在'
            ]));
        }
        $meeting_details_info = iterator_to_array($meeting_details_info->getIterator());

        if($meeting_details_info['type'] == self::DETAIL_TYPE_ONE){
            //会议介绍
            //form control empty
            if(!$this->formControlEmpty(['content'], $post)){
                return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                    'status'  => false,
                    'message' => '必填参数错误'
                ]));
            }
            $update_row = [
                'content' => $post['content'],
                'sort'    => $post['sort'],
                'type'    => $post['type'],
            ];
            $result     = $this->db->meeting_details()->where(['id' => $post['id']])->update($update_row);
            $return     = $result ? true : false;
        }else{
            if($meeting_details_info['type'] == self::DETAIL_TYPE_TWO){
                //会议日程
                //日程添加 schedule
                if(!$this->formControlEmpty(['schedule'], $post) || !is_array($post['schedule'])){
                    return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                        'status'  => false,
                        'message' => '必填参数错误'
                    ]));
                }
                $this->db->transaction   = 'BEGIN';
                $meeting_schedule_delete = $this->db->meeting_schedule()->where(['meeting_id' => $post['meeting_id']])->delete();
                $schedule_result         = $this->_insert_schedule($post);
                $update_row              = [
                    'sort' => $post['sort'],
                    'type' => $post['type'],
                ];
                $result                  = $this->db->meeting_details()->where(['id' => $post['id']])->update($update_row);
                if($meeting_schedule_delete !== false && $result !== false && $schedule_result !== false){
                    $this->db->transaction = 'COMMIT';
                    $return                = true;
                }else{
                    $this->db->transaction = 'ROLLBACK';
                    $return                = false;
                }
            }else{
                if($meeting_details_info['type'] == self::DETAIL_TYPE_THREE){
                    //会议嘉宾
                    //嘉宾添加
                    if(!$this->formControlEmpty(['head', 'name', 'remark'], $post) || !is_array($post['schedule'])){
                        return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                            'status'  => false,
                            'message' => '必填参数错误'
                        ]));
                    }
                    // transaction
                    $this->db->transaction = 'BEGIN';
                    $meeting_guest_delete  = $this->db->meeting_guest()->where(['meeting_id' => $post['meeting_id']])->delete();
                    $guest_result          = $this->_insert_guest($post);
                    $update_row            = [
                        'sort' => $post['sort'],
                        'type' => $post['type'],
                    ];
                    $result                = $this->db->meeting_details()->where(['id' => $post['id']])->update($update_row);
                    if($meeting_guest_delete !== false && $result !== false && $guest_result !== false){
                        $this->db->transaction = 'COMMIT';
                        $return                = true;
                    }else{
                        $this->db->transaction = 'ROLLBACK';
                        $return                = false;
                    }
                }else{
                    if($meeting_details_info['type'] == self::DETAIL_TYPE_FOUR){
                        //会议邀请函
                        //form control empty
                        if(!$this->formControlEmpty(['photo'], $post)){
                            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                                'status'  => false,
                                'message' => '必填参数错误'
                            ]));
                        }
                        $update_row = [
                            'sort' => $post['sort'],
                            'type' => $post['type'],
                        ];
                        //邀请函处理
                        $uploadInvitation = $this->uploadBase64Image($post['photo'], '', 'meeting/detail/invitation', 'detail.invitation');
                        if(!empty($uploadInvitation)){
                            //上传新图片
                            $insert_row['real_photo'] = $uploadInvitation['realPath'];
                            $insert_row['photo']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadInvitation['photo'];
                        }
                        $result = $this->db->meeting_details()->where(['id' => $post['id']])->update($update_row);
                        $return = $result ? true : false;
                    }else{
                        return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                            'status'  => false,
                            'message' => '参数错误'
                        ]));
                    }
                }
            }
        }

        $return = $return ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 发布 */
    public function actionRelease(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id'], $post)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        //check
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }
        $update_row = [
            'is_release' => 2
        ];
        $result     = $this->db->meeting()->where(['id' => $post['id']])->update($update_row);
        $return     = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 报名开关状态更新 */
    public function actionEnroll(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id', 'enroll_state'], $post) || !in_array($post['enroll_state'], [1, 2])){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }
        $update_row = [
            'enroll_state' => $post['enroll_state']
        ];
        $result     = $this->db->meeting()->where(['id' => $post['id']])->update($update_row);
        $return     = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 会议开关更新 */
    public function actionState(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty(['id', 'state'], $post) || !in_array($post['state'], [1, 2])){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }
        $update_row = [
            'state' => $post['state']
        ];
        $result     = $this->db->meeting()->where(['id' => $post['id']])->update($update_row);
        $return     = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response){
        $post = $request->getParsedBody();
        //form control empty
        if(!$this->formControlEmpty([
            'id',
            'name',
            'contacts',
            'mobile',
            'enroll_start',
            'enroll_end',
            'time_start',
            'time_end',
            'attend_time',
            'address',
            'venue_name',
            'is_credit',
            'credis',
            'type',
            'subject'
        ], $post)
        ){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $post['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if(!$meeting_info){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '会议不存在'
            ]));
        }
        $meeting_info = iterator_to_array($meeting_info->getIterator());

        $update_row = [
            'uid_admin'    => $cache_user_info['id'],
            'contacts'     => $post['contacts'],
            'mobile'       => $post['mobile'],
            'name'         => $post['name'],
            'name_english' => $post['name_english'] ? $post['name_english'] : '',
            'enroll_start' => strtotime($post['enroll_start']),
            'enroll_end'   => strtotime($post['enroll_end']),
            'time_start'   => strtotime($post['time_start']),
            'time_end'     => strtotime($post['time_end']),
            'attend_time'  => strtotime($post['attend_time']),
            'address'      => $post['address'],
            'venue_name'   => $post['venue_name'],
            'is_credit'    => $post['is_credit'],
            'credis'       => $post['credis'],
            'type'         => $post['type'],
            'subject'      => $post['subject'],
        ];
        //banner处理
        if(!empty($post['banner'])){
            $uploadBanner = $this->uploadBase64Image($post['banner'], '', 'meeting/banner', 'banner');
            if(!empty($uploadBanner)){
                //删除原图片
                @unlink($meeting_info['real_banner']);
                //上传新图片
                $update_row['real_banner'] = $uploadBanner['realPath'];
                $update_row['banner']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadBanner['photo'];
            }
        }

        //icon处理
        if(!empty($post['icon'])){
            $uploadIcon = $this->uploadBase64Image($post['icon'], '', 'meeting/icon', 'icon');
            if(!empty($uploadIcon)){
                //删除原图片
                @unlink($meeting_info['real_icon']);
                //上传新图片
                $update_row['real_icon'] = $uploadIcon['realPath'];
                $update_row['icon']      = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . $uploadIcon['photo'];
            }
        }

        $result = $this->db->meeting()->where(['id' => $post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* info */
    public function actionInfo(Request $request, Response $response){
        $get = $request->getQueryParams();
        //form control empty
        if(!$this->formControlEmpty(['id'], $get)){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode([
                'status'  => false,
                'message' => '必填参数错误'
            ]));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $meeting_info    = $this->db->meeting()->where([
            'id'        => $get['id'],
            'uid_admin' => $cache_user_info['id'],
            'status'    => 1
        ])->fetch();
        if($meeting_info){
            $meeting_info = iterator_to_array($meeting_info->getIterator());
            //detail
            $meeting_info['detail_info'] = $this->iterator_array($this->db->meeting_details()->select('')->where(['meeting_id' => $get['id']])->order('sort asc'));
            $return                      = [
                'status'  => true,
                'message' => '',
                'data'    => $meeting_info
            ];
        }else{
            $return = [
                'status'  => false,
                'message' => "会议不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}