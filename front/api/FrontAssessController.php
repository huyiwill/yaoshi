<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class FrontAssessController
 * @package front\api
 */
class FrontAssessController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* list */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = [
            'uid' => $cache_user_info['id'],
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'id asc';
        $count = $this->db->user_assess()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->user_assess()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function(&$item){
                $assess_info = $this->db->assess()->where(['id'=>$item['assess_id']])->fetch();
                $item['assess_info'] = $assess_info ? iterator_to_array($assess_info->getIterator()) : [];
                //未考核 && 考核已过期 状态为 已结束
                if ($item['state'] == AssessUserController::STATE_ONE && $assess_info['end_time'] < time()) {
                    $item['state'] = AssessUserController::STATE_FIVE;
                }
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

    /* 开始考核 */
    public function actionStart(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','assess_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $assess_info = $this->db->assess()->where(['id' => $post['assess_id'],'status'=>1])->fetch();
        if (!$assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'本次考核不存在']));
        }
        $assess_info = iterator_to_array($assess_info->getIterator());
        $user_assess_info = $this->db->user_assess()->where(['id'=>$post['id'],'uid' => $cache_user_info['id'],'assess_id'=>$assess_info['id'],'status'=>1])->fetch();
        //考核过期时间验证
        if ($assess_info['end_time'] < time()) {
            //已结束
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'考核已结束']));
        }
        //考核频率限制 单人单次 开始时间 > 0 则已经考过
        if ($user_assess_info['start_time'] > 0) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'已完成本次考核']));
        }
        if (!$user_assess_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'考核关系不对应']));
        }

        $user_assess_update_result = $this->db->user_assess()->where(['id'=>$user_assess_info['id']])->update(['start_time'=>time()]);
        if (!$user_assess_update_result) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'服务器错误']));
        }

        $order = 'sort asc';
        $group = 'test_id';
        $test_id = $assess_info['test_id'];
        //试卷信息
        $test_info = $this->db->test()->where(['id'=>$test_id,'status'=>1])->fetch();
        if ($test_info) {
            $test_info = iterator_to_array($test_info->getIterator());
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        //总题数
        $count = $this->db->test_subject()->select('')
            ->where(['test_id' => $test_id,'status'=>1])->count();
        //总分数
        $total = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'status'=>1])->group($group));
        $total = empty($total) ? 0 : $total[0]['total'];
        //单选题
        $subject_one = $this->iterator_array($this->db->test_subject()->select('id,name,choice,score,category_id,pharmacy_id')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->order($order));
        $total_one = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->group($group));
        $total_one = empty($total_one) ? 0 : $total_one[0]['total'];
        //多选
        $subject_two = $this->iterator_array($this->db->test_subject()->select('id,name,choice,score,category_id,pharmacy_id')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->order($order));
        $total_two = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->group($group));
        $total_two = empty($total_two) ? 0 : $total_two[0]['total'];
        //处方审核
        $subject_four = $this->iterator_array($this->db->test_subject()->select('id,name,photo,score,category_id,pharmacy_id')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->order($order));
        $total_four = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->group($group));
        $total_four = empty($total_four) ? 0 : $total_four[0]['total'];
        //用药交代
        $subject_five = $this->iterator_array($this->db->test_subject()->select('id,name,photo,score,category_id,pharmacy_id')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->order($order));
        $total_five = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->group($group));
        $total_five = empty($total_five) ? 0 : $total_five[0]['total'];
        //问答
        $subject_six = $this->iterator_array($this->db->test_subject()->select('id,name,score,category_id,pharmacy_id')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->order($order));
        $total_six = $this->iterator_array($this->db->test_subject()->select('SUM(`score`) as total')
            ->where(['test_id' => $test_id,'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->group($group));
        $total_six = empty($total_six) ? 0 : $total_six[0]['total'];
        if (!empty($subject_one)) {
            array_walk($subject_one, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }
        if (!empty($subject_two)) {
            array_walk($subject_two, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }
        $return = [
            'status' => true,
            'data' => [
                'count' => $count,
                'total' => $total,
                'test_info' => $test_info,
                'assess_info' => $assess_info,
                'subject_one' => $subject_one,
                'subject_two' => $subject_two,
                'subject_four' => $subject_four,
                'subject_five' => $subject_five,
                'subject_six' => $subject_six,
                'total_one' => $total_one,
                'total_two' => $total_two,
                'total_four' => $total_four,
                'total_five' => $total_five,
                'total_six' => $total_six,
            ]
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /*提交考核*/
    public function actionEnd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id','assess_id','test_subject'], $post) || !is_array($post['test_subject'][0]['text_msg'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $user_asses_info = $this->db->user_assess()->where(['id'=>$post['id'],'uid'=>$cache_user_info['id'],'assess_id'=>$post['assess_id']])->fetch();
        if (!$user_asses_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法答题']));
        }
        $user_asses_info = iterator_to_array($user_asses_info->getIterator());
        $test_id = $this->db->assess()->where(['id'=>$user_asses_info['assess_id']])->fetch('test_id');
        $one_score = 0;
        $two_score = 0;
        foreach ($post['test_subject'][0]['text_msg'] as $key=>$item) {
            //right key
            $test_subject_info = $this->db->test_subject()->where(['test_id'=>$test_id, 'id'=>$item['id']])->fetch();
            if (!$test_subject_info) {
                continue ;
            }
            $test_subject_info = iterator_to_array($test_subject_info->getIterator());
            $post['test_subject'][0]['text_msg'][$key]['right_key'] = $test_subject_info['right_key'];
            if (empty($item['do'])) {
                continue ;
            }
            if ($item['topic_type'] == SubjectController::SUBJECT_ONE) {
                //单选
                $post['test_subject'][0]['text_msg'][$key]['score'] = $test_subject_info['score'];
                if ($item['do'] == $test_subject_info['right_key']) {
                    $post['test_subject'][0]['text_msg'][$key]['do_right'] = true;
                    $one_score += $test_subject_info['score'];
                } else {
                    $post['test_subject'][0]['text_msg'][$key]['do_right'] = false;
                }
            } else if ($item['topic_type'] == SubjectController::SUBJECT_TWO) {
                //多选
                $post['test_subject'][0]['text_msg'][$key]['score'] = $test_subject_info['score'];
                if ($item['do'] == $test_subject_info['right_key']) {
                    $post['test_subject'][0]['text_msg'][$key]['do_right'] = true;
                    $two_score += $test_subject_info['score'];
                } else {
                    $post['test_subject'][0]['text_msg'][$key]['do_right'] = false;
                }
            }
        }

        $post['test_subject'][0]['score'] = $one_score + $two_score;
        //修改
        $update_row = [
            'test_subject' => json_encode($post['test_subject']),
            'answer_time' => (int)round((((time() - $user_asses_info['start_time'])%86400)%3600)/60),
            'theory_score' => $one_score + $two_score,
            'one_score' => $one_score,
            'two_score' => $two_score,
            'state' => AssessUserController::STATE_TWO,
            'complete_time' => time(),
        ];
        $update_row['complete_status'] = empty($post['complete_status']) ? 1 : 2;
        $result = $this->db->user_assess()->where(['id'=>$post['id']])->update($update_row);
        $return = $result ? ['status' => true, 'message' => '操作成功', 'data'=> ['theory_score' => $update_row['theory_score'],'answer_time'=>$update_row['answer_time']]] : ['status' => false, 'message' => '操作失败'];
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
        $user_assess_info = $this->db->user_assess()->where(['id' => $get['id'], 'uid' => $cache_user_info['id'], 'status'=>[1]])->fetch();
        if ($user_assess_info['complete_status'] == 1 && $user_assess_info['state'] <= AssessUserController::STATE_THREE) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非正常交卷,不能查看答案']));
        }
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