<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 历年真题
 * Class ExercisesController
 * @package front\api
 */
class ExercisesController extends Controller
{
    const TYPE_ONE = 1; //关闭/未销售
    const TYPE_TWO = 2; //开启

    const DISCOUNT_ONE = 1; //历年真题 打折
    const DISCOUNT_TWO = 2; //历年真题 不打折

    private $_limit = 10;

    /* 历年真题 */
    public function actionList(Request $request, Response $response)
    {
        //销售中
        //未过期
        //未删除/禁用
        $get = $request->getQueryParams();
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $search = ['test_end >= ?' => time(), 'type' => self::TYPE_TWO, 'status' => 1];
        $limit = 2;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $order = 'id asc';
        $count = $this->db->exercises()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->iterator_array($this->db->exercises()->select('id,name,time,remark,price,price,is_discount,discount,discount_end,test_end,photo')
            ->where($search)->order($order)->limit($limit, ($page - 1) * $limit));
        if (!empty($result)) {
            foreach ($result as $key=>$item) {
                //免费 || 已购买
                $user_exercises_id = $this->db->user_exercises()->where(['uid'=>$cache_user_info['id'], 'exercises_id'=>$item['id'], 'status'=>1])->fetch('id');
                if ($item['price'] <= 0) {
                    $result[$key]['can'] = 1; //可以直接答题
                } else if (!empty($user_exercises_id)) {
                    $result[$key]['can'] = 2; //已购买可以直接答题
                } else {
                    $result[$key]['can'] = 3; //不可以直接答题
                }
            }
        }
        $return = [
            'status' => true,
            'current' => $page,
            'total' => $number,
            'data' => $result,
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 开始答题 */
    public function actionStart(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        //真题记录 exercises_do
        $do = $this->db->exercises_do()->where(['uid'=>$cache_user_info['id'], 'exercises_id'=>$post['id']])->fetch('id');
        if (!$do) {
            $this->db->exercises_do()->insert([
                'uid' => $cache_user_info['id'],
                'exercises_id' => $post['id'],
                'create_time' => time()
            ]);
        }
        //check free or already purchased
        $exercises_price = $this->db->exercises()->where(['id'=>$post['id']])->fetch('price');
        $user_exercises_id = $this->db->user_exercises()->where(['uid'=>$cache_user_info['id'], 'exercises_id'=>$post['id'], 'status'=>1])->fetch('id');
        if ($exercises_price > 0 && empty($user_exercises_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'请先购买该真题']));
        }
        $order = 'sort asc';
        //历年真题信息
        $exercises_info = $this->db->exercises()->where(['id'=>$post['id']])->fetch();
        if ($exercises_info) {
            $exercises_info = iterator_to_array($exercises_info->getIterator());
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        //总题数
        $count = $this->db->exercises_subject()->select('')
            ->where(['exercises_id' => $post['id'],'status'=>1])->count();
        //总分数
        $total = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'status'=>1])->group('exercises_id'));
        $total = empty($total) ? 0 : $total[0]['total'];
        //单选题
        $subject_one = $this->iterator_array($this->db->exercises_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->order($order));
        $total_one = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_ONE,'status'=>1])->group('exercises_id'));
        $total_one = empty($total_one) ? 0 : $total_one[0]['total'];
        //多选
        $subject_two = $this->iterator_array($this->db->exercises_subject()->select('id,name,choice,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->order($order));
        $total_two = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_TWO,'status'=>1])->group('exercises_id'));
        $total_two = empty($total_two) ? 0 : $total_two[0]['total'];
        //处方审核
        $subject_four = $this->iterator_array($this->db->exercises_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->order($order));
        $total_four = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_FOUR,'status'=>1])->group('exercises_id'));
        $total_four = empty($total_four) ? 0 : $total_four[0]['total'];
        //用药交代
        $subject_five = $this->iterator_array($this->db->exercises_subject()->select('id,name,photo,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->order($order));
        $total_five = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_FIVE,'status'=>1])->group('exercises_id'));
        $total_five = empty($total_five) ? 0 : $total_five[0]['total'];
        //问答
        $subject_six = $this->iterator_array($this->db->exercises_subject()->select('id,name,right_key,score,category_id,pharmacy_id')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->order($order));
        $total_six = $this->iterator_array($this->db->exercises_subject()->select('SUM(`score`) as total')
            ->where(['exercises_id' => $post['id'],'topic_type' => SubjectController::SUBJECT_SIX,'status'=>1])->group('exercises_id'));
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
                'exercises_info' => $exercises_info,
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

    /* 交卷 */
    public function actionEnd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['exercises_id','exercises_subject','answer_time','score'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        //has record ?
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $exercises_record_info = $this->db->exercises_record()->where(['uid'=>$cache_user_info['id'],'exercises_id'=>$post['exercises_id'],'status' => [1,2]])->fetch();
        if ($exercises_record_info) {
            $exercises_record_info = iterator_to_array($exercises_record_info->getIterator());
            $id = $exercises_record_info['id'];
            //update
            $update_row = [
                'exercises_subject' => empty($post['exercises_subject']) ? '' : json_encode($post['exercises_subject']),
                'answer_time' => $post['answer_time'],
                'score' => $post['score'],
                'update_time' => time(),
            ];
            $result = $this->db->exercises_record()->where(['id'=>$id])->update($update_row);
        } else {
            //insert
            $insert_row = [
                'uid' => $cache_user_info['id'],
                'exercises_id' => $post['exercises_id'],
                'exercises_subject' => empty($post['exercises_subject']) ? '' : json_encode($post['exercises_subject']),
                'answer_time' => $post['answer_time'],
                'score' => $post['score'],
                'update_time' => time(),
                'create_time' => time(),
            ];
            $result = $this->db->exercises_record()->insert($insert_row);
            $id = $this->db->exercises_record()->insert_id();
        }
        $return = $result ? ['status' => true, 'message' => '操作成功', 'data' => $id] : ['status' => false, 'message' => '操作失败'];
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
        $exercises_info = $this->db->exercises()->where(['id' => $get['id'], 'status'=>1])->fetch();
        if ($exercises_info) {
            $exercises_info = iterator_to_array($exercises_info->getIterator());
            //免费 || 已购买
            $user_exercises_id = $this->db->user_exercises()->where(['uid'=>$cache_user_info['id'], 'exercises_id'=>$exercises_info['id'], 'status'=>1])->fetch('id');
            if ($exercises_info['price'] <= 0 || !empty($user_exercises_id)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'请直接答题']));
            }
            $return = [
                'status' => true,
                'message' => '',
                'data' => $exercises_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "真题不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}