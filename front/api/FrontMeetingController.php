<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class FrontMeetingController
 * @package front\api
 */
class FrontMeetingController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* 资料下载 */
    public function actionDownload(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['real_path','filename'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
//        $file_sub_path=$_SERVER['DOCUMENT_ROOT']."/data/www/yaoshi/upload/meeting/data/";
//        $file_path=$file_sub_path.$file_name;
        //首先要判断给定的文件存在与否
        if(!file_exists($get['real_path'])){
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'文件不存在']));
        }
        $fp=fopen($get['real_path'],"r");
        $file_size=filesize($get['real_path']);
        //下载文件需要用到的头
        Header("Content-type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Accept-Length:".$file_size);
        Header("Content-Disposition: attachment; filename=".$get['filename']);
        $buffer=1024;
        $file_count=0;
        //向浏览器返回数据
        while(!feof($fp) && $file_count<$file_size){
            $file_con=fread($fp,$buffer);
            $file_count+=$buffer;
            echo $file_con;
        }
        fclose($fp);
    }

    /* 会议报名 */
    public function actionEnroll(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['meeting_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $search = [
            'id' => $post['meeting_id'],
            'examine_type' => MeetingController::EXAMINE_TYPE_TWO,
            'time_end >= ?' => time(),
            'state' => 1,
            'status'=>1
        ];
        $meeting_info = $this->db->meeting()->where($search)->fetch();
        if (!$meeting_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'会议不存在']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        //check
        $meeting_user_id = $this->db->meeting_user()->where(['meeting_id'=>$post['meeting_id'],'uid'=>$cache_user_info['id'],'status'=>[1,2]])->fetch('id');
        if ($meeting_user_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'该会议已报名']));
        }
        $insert_row = [
            'uid' => $cache_user_info['id'],
            'meeting_id' => $post['meeting_id'],
            'create_time' => time()
        ];
        $result = $this->db->meeting_user()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 嘉宾信息 */
    public function actionGuestInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id','meeting_id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $meeting_guest_info = $this->db->meeting_guest()->where(['id' => $get['id'],'meeting_id' => $get['meeting_id']])->fetch();
        if ($meeting_guest_info) {
            $meeting_guest_info = iterator_to_array($meeting_guest_info->getIterator());
            $return = [
                'status' => true,
                'message' => '',
                'data' => $meeting_guest_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "嘉宾不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* detail info */
    public function actionDetailInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['meeting_id','type'], $get) || !in_array($get['type'],[2,3])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $search = [
            'id' => $get['meeting_id'],
            'examine_type' => MeetingController::EXAMINE_TYPE_TWO,
            'time_end >= ?' => time(),
            'state' => 1,
            'status'=>1
        ];
        $meeting_info = $this->db->meeting()->where($search)->fetch();
        if (!$meeting_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'会议不存在']));
        }

        if ($get['type'] == MeetingController::DETAIL_TYPE_TWO) {
            //日程
            $return = $this->iterator_array($this->db->meeting_schedule()->select('')->where(['meeting_id'=>$get['meeting_id']])->order('days asc'));
        } else {
            //嘉宾
            $return = $this->iterator_array($this->db->meeting_guest()->select('')->where(['meeting_id'=>$get['meeting_id']])->order('create_time asc'));
        }
        $return = [
            'status' => true,
            'message' => '',
            'data' => $return
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* list */
    public function actionList(Request $request, Response $response)
    {
        //未结束
        //未关闭
        //审核通过
        $get = $request->getQueryParams();
        $search = [
            'province_id' => $get['province_id'] ? $get['province_id'] : '',
            'city_id' => $get['city_id'] ? $get['city_id'] : '',
            'examine_type' => MeetingController::EXAMINE_TYPE_TWO,
            'time_end >= ?' => time(),
            'state' => 1
        ];
        if (!empty($get['start']) && !empty($get['end'])) {
            $search['time_start >= ?'] = strtotime($get['start'] . '00:00:00');
            $search['time_end <= ?'] = strtotime($get['end'] . '23:59:59');
        }
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->meeting()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->meeting()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
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
        $search = [
            'id' => $get['id'],
            'examine_type' => MeetingController::EXAMINE_TYPE_TWO,
            'time_end >= ?' => time(),
            'state' => 1,
            'status'=>1
        ];
        $meeting_info = $this->db->meeting()->where($search)->fetch();
        if ($meeting_info) {
            $meeting_info = iterator_to_array($meeting_info->getIterator());
            $meeting_info['data'] = empty($meeting_info['data']) ? '' : json_decode($meeting_info['data'], true);
            //detail
            $meeting_info['detail_info'] = $this->iterator_array($this->db->meeting_details()->select('')->where(['meeting_id'=>$get['id']])->order('sort asc'));
            $return = [
                'status' => true,
                'message' => '',
                'data' => $meeting_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "会议不存在/已过期"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}