<?php

namespace front\api;

use front\library\Tools;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class OnlineController
 * @package front\api
 */
class OnlineController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* 线上实战列表 */
    public function actionList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!empty($get['topic_type']) && !in_array($get['topic_type'], [4,5,6])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }
        $search = [
            'topic_type' => $get['topic_type'] ? $get['topic_type'] : [4,5,6],
            'therapeutic' => $get['therapeutic'] ? $get['therapeutic'] : '',
        ];
        $limit = 8;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'create_time desc';
        $count = $this->db->subject()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->subject()->select('id, name, topic_type, right_key, score, degree, price, is_price')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            array_walk($data, function(&$item) {
                $item['comments_count'] = $this->db->practice_comment()->select('')
                    ->where(['subject_id'=>$item['subject_id'],'answer_id'=>$item['id'],'synchro'=>1,'status'=>1])->count();
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

    /* 线上实战详情 */
    public function actionDetail(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $subject_info = $this->db->subject()->where(['id' => $get['id'], 'status'=>1])->fetch();
        if ($subject_info) {
            $subject_info = iterator_to_array($subject_info->getIterator());
            //获取该题目下的患者信息
            $patient_info = $this->db->patient()->where(['subject_id' => $subject_info['id'], 'status'=>[1,2]])->fetch();
            $subject_info['patient_info'] = $patient_info ? iterator_to_array($patient_info->getIterator()) : [];
            unset($subject_info['analysis']);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $subject_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "题目不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 提交答案 */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['subject_id','user_answer'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $subject_info = $this->db->subject()->where(['id'=>$post['subject_id'],'status'=>1])->fetch();
        if (empty($subject_info)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }
        $subject_info = iterator_to_array($subject_info->getIterator());
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $insert_row = [
            'uid' => $cache_user_info['id'],
            'subject_id' => $post['subject_id'],
            'user_answer' => $post['user_answer'],
            'synchro' => $post['synchro'] == 1 ? $post['synchro'] : 2,
            'create_time' => time()
        ];
        $this->db->transaction = 'BEGIN';
        $result = $this->db->practice_answer()->insert($insert_row);
        //热度
        $weight = $subject_info['weight'] + 1;
        $subject_update_result = $this->db->subject()->where(['id'=>$post['subject_id']])->update(['weight'=>$weight]);
        if ($result !== false && $subject_update_result !== false) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 前台答案列表 */
    public function actionAnswerList(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $search = [
            'subject_id' => $get['subject_id'],
            'synchro' => 1
        ];
        $limit = $this->_limit;
        $page = !empty($get['page']) ? $get['page'] : 1;
        $search = $this->_commonSearch($search, $this->_search_black);
        $order = !empty($get['order']) ? $get['order'] : 'create_time desc';
        $count = $this->db->practice_answer()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->practice_answer()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
        $data = $this->iterator_array($result);
        if (!empty($data)) {
            //问题下的评论列表
            array_walk($data, function(&$item) {
                $sql = "SELECT pc.*,ug.nickname,ug.head FROM `ys_practice_comment` pc, `ys_user_global` ug
                  WHERE pc.uid = ug.id AND pc.synchro = 1 AND pc.status = 1 AND pc.subject_id = :subject_id AND pc.answer_id = :answer_id";
                $sth = $this->pdo->prepare($sql);
                $sth->bindParam(':subject_id', $item['subject_id'], \PDO::PARAM_INT);
                $sth->bindParam(':answer_id', $item['id'], \PDO::PARAM_INT);
                $sth->execute();
                $comments_tree = $sth->fetchAll(\PDO::FETCH_ASSOC);

                $comments_tree = Tools::getTree($comments_tree);
                $comments_count = $this->db->practice_comment()->select('')
                    ->where(['subject_id'=>$item['subject_id'],'answer_id'=>$item['id'],'synchro'=>1,'status'=>1])->count();
                $item['comments_tree'] = $comments_tree;
                $item['comments_count'] = $comments_count;
                $user_info = $this->db->user_global()->where(['id'=>$item['uid']])->fetch();
                if ($user_info) {
                    $user_info = iterator_to_array($user_info->getIterator());
                    unset($user_info['password']);
                } else {
                    $user_info = [];
                }
                $item['user_info'] = $user_info;
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

    /* 提交评论 */
    public function actionCommentAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['subject_id','answer_id','pid','content'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $subject_id = $this->db->subject()->where(['id'=>$post['subject_id'],'status'=>1])->fetch('id');
        if (empty($subject_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }
        $practice_answer_subject_id = $this->db->practice_answer()->where(['id'=>$post['answer_id'],'status'=>1])->fetch('subject_id');
        if (empty($practice_answer_subject_id) || $practice_answer_subject_id != $post['subject_id']) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'答案不存在']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        $insert_row = [
            'uid' => $cache_user_info['id'],
            'subject_id' => $post['subject_id'],
            'answer_id' => $post['answer_id'],
            'pid' => $post['pid'],
            'content' => $post['content'],
            'synchro' => $post['synchro'] == 1 ? $post['synchro'] : 2,
            'type' => 1,
            'create_time' => time()
        ];
        $result = $this->db->practice_comment()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 点赞 */
    public function actionThumbsUp(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['subject_id','relation_id','type'], $post)
            || !in_array($post['type'], [1,2])
        ) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if ($post['type'] == 1) {
            //答案
            $relation_info = $this->db->practice_answer()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->fetch();
        } else {
            //评论
            $relation_info = $this->db->practice_comment()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->fetch();
        }
        if (!$relation_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'数据不存在']));
        }
        $current_day = date('Y-m-d', time());
        $thumbs_up_info = $this->db->thumbs_up()->where([
            'uid'=>$cache_user_info['id'],
            'subject_id'=>$post['subject_id'],
            'relation_id'=>$post['relation_id'],
            'type'=>$post['type'],
            'day'=>$current_day,
        ])->fetch();

        $this->db->transaction = 'BEGIN';
        if ($thumbs_up_info) {
            $thumbs_up_info = iterator_to_array($thumbs_up_info->getIterator());
            if ($thumbs_up_info['state'] == 1) {
                //原来加
                $number = $relation_info['number'] - 1;
                $state = 2;
            } else {
                //原来减
                $number = $relation_info['number'] + 1;
                $state = 1;
            }
            $thumbs_up_update_result = $this->db->thumbs_up()->where([
                'uid'=>$cache_user_info['id'],
                'subject_id'=>$post['subject_id'],
                'relation_id'=>$post['relation_id'],
                'type'=>$post['type'],
                'day'=>$current_day,])->update(['state'=>$state]);
            if ($post['type'] == 1) {
                //答案
                $relation_update_result = $this->db->practice_answer()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->update(['number'=>$number]);
            } else {
                //评论
                $relation_update_result = $this->db->practice_comment()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->update(['number'=>$number]);
            }
            if ($thumbs_up_update_result !== false && $relation_update_result !== false) {
                $this->db->transaction = 'COMMIT';
                $return = ['status' => true, 'message' => '操作成功'];
            } else {
                $this->db->transaction = 'ROLLBACK';
                $return = ['status'=> false, 'message' => '操作失败'];
            }
        } else {
            $thumbs_up_insert_row = [
                'uid'=>$cache_user_info['id'],
                'subject_id'=>$post['subject_id'],
                'relation_id'=>$post['relation_id'],
                'type'=>$post['type'],
                'day'=>$current_day,
                'create_time'=>time()
            ];
            $thumbs_up_insert_result = $this->db->thumbs_up()->insert($thumbs_up_insert_row);
            $number = $relation_info['number'] + 1;
            if ($post['type'] == 1) {
                //答案
                $relation_update_result = $this->db->practice_answer()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->update(['number'=>$number]);
            } else {
                //评论
                $relation_update_result = $this->db->practice_comment()->where(['id'=>$post['relation_id'],'uid'=>$cache_user_info['id'],'subject_id'=>$post['subject_id'],'status'=>1])->update(['number'=>$number]);
            }
            if ($thumbs_up_insert_result !== false && $relation_update_result !== false) {
                $this->db->transaction = 'COMMIT';
                $return = ['status' => true, 'message' => '操作成功'];
            } else {
                $this->db->transaction = 'ROLLBACK';
                $return = ['status'=> false, 'message' => '操作失败'];
            }
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));

    }
}