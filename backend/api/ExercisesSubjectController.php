<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class ExercisesSubjectController
 * @package backend\api
 */
class ExercisesSubjectController extends Controller
{
    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['topic_type','exercises_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $public_form = ['name','right_key','score','pharmacy_id'];
        $current_order = $this->iterator_array($this->db->exercises_subject()->select('Max(sort) as sort')->where(['exercises_id'=>$post['exercises_id']]));
        $current_order = $current_order[0]['sort'] ? $current_order[0]['sort'] : 0;
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        if ($post['topic_type'] == SubjectController::SUBJECT_ONE || $post['topic_type'] == SubjectController::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'exercises_id' => $post['exercises_id'],
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];
            $result = $this->db->exercises_subject()->insert($insert_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_FOUR || $post['topic_type'] == SubjectController::SUBJECT_FIVE) {
            //处方 || 用药交代
            if (!$this->formControlEmpty(array_merge(['photo'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'exercises_id' => $post['exercises_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];

            //处方图片处理
            $uploadResult = $this->uploadBase64Image($post['photo'], '', 'exercises/subject', 'subject_prescription');
            if (!empty($uploadResult)) {
                //上传新图片
                $insert_row['real_photo'] = $uploadResult['realPath'];
                $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
            $result = $this->db->exercises_subject()->insert($insert_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'exercises_id' => $post['exercises_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];
            $result = $this->db->exercises_subject()->insert($insert_row);
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }

        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* choice */
    public function actionChoice(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['subjects','exercises_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $current_order = $this->iterator_array($this->db->exercises_subject()->select('Max(sort) as sort')->where(['exercises_id'=>$post['exercises_id']]));
        $current_order = $current_order[0]['sort'] ? $current_order[0]['sort'] : 0;
        $count = count($post['subjects']);
        $i = 0;
        if (is_array($post['subjects'])) {
            foreach ($post['subjects'] as $key=>$id) {
                $current_order++;
                $subject_info = $this->db->subject()->where(['id'=>$id, 'status'=>[1,2]])->fetch();
                if ($subject_info) {
                    $subject_info = iterator_to_array($subject_info->getIterator());
                    $insert_row = [
                        'exercises_id' => $post['exercises_id'],
                        'name' => $subject_info['name'],
                        'topic_type' => $subject_info['topic_type'],
                        'choice' => $subject_info['choice'],
                        'right_key' => $subject_info['right_key'],
                        'score' => $post['score'] ? $post['score'] : $subject_info['score'],
                        'role' => $subject_info['role'],
                        'post' => $subject_info['post'],
                        'degree' => $subject_info['degree'],
                        'category_id' => $subject_info['category_id'],
                        'pharmacy_id' => $subject_info['pharmacy_id'],
                        'therapeutic' => $subject_info['therapeutic'],
                        'analysis' => $subject_info['analysis'],
                        'price' => $subject_info['price'],
                        'is_price' => $subject_info['is_price'],
                        'photo' => $subject_info['photo'],
                        'real_photo' => $subject_info['real_photo'],
                        'sort' => $current_order,
                        'create_time' => time(),
                    ];
                    $result = $this->db->exercises_subject()->insert($insert_row);
                    !$result ? : $i++;
                }
            }
        }
        $return = $i==$count ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* order */
    public function actionOrder(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['change_one','change_two'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $one_info = $this->db->exercises_subject()->where(['id'=>$post['change_one'],'status'=>1])->fetch();
        $two_info = $this->db->exercises_subject()->where(['id'=>$post['change_two'],'status'=>1])->fetch();
        if (!$one_info || !$two_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'交换数据不存在']));
        }
        $one_info = iterator_to_array($one_info->getIterator());
        $two_info = iterator_to_array($two_info->getIterator());
        $this->db->transaction = 'BEGIN';
        $one_result = $this->db->exercises_subject()->where(['id'=>$one_info['id']])->update(['sort'=>$two_info['sort']]);
        $two_result = $this->db->exercises_subject()->where(['id'=>$two_info['id']])->update(['sort'=>$one_info['sort']]);
        if ($one_result && $two_result) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status'=> false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 禁用/启用 */
    public function actionStatus(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'status'], $post) || !in_array($post['status'], [1,2])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $exercises_subject_id = $this->db->exercises_subject()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($exercises_subject_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->exercises_subject()->where(['id' => $post['id']])->update($update_row);
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
        $result = $this->db->exercises_subject()->where(['id' => $post['id']])->update($update_row);
        if ($result) {
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $public_form = ['id','name','right_key','score','pharmacy_id'];

        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        if ($post['topic_type'] == SubjectController::SUBJECT_ONE || $post['topic_type'] == SubjectController::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $update_row = [
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
            ];
            $result = $this->db->exercises_subject()->where(['id'=>$post['id']])->update($update_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_FOUR || $post['topic_type'] == SubjectController::SUBJECT_FIVE) {
            //处方 || 用药交代
            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
            ];

            //处方图片处理
            if (!empty($post['photo'])) {
                $uploadResult = $this->uploadBase64Image($post['photo'], '', 'exercises/subject', 'subject_prescription');
                if (!empty($uploadResult)) {
                    //删除旧图片
                    $exercises_subject_info = $this->db->exercises_subject()->where(['id'=>$post['id']])->fetch();
                    if ($exercises_subject_info) {
                        $exercises_subject_info = iterator_to_array($exercises_subject_info->getIterator());
                        @unlink($exercises_subject_info['real_photo']);
                    }
                    //上传新图片
                    $update_row['real_photo'] = $uploadResult['realPath'];
                    $update_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
                }
            }
            $result = $this->db->exercises_subject()->where(['id'=>$post['id']])->update($update_row);
        } else if ($post['topic_type'] == SubjectController::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
            ];
            $result = $this->db->exercises_subject()->where(['id'=>$post['id']])->update($update_row);
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }

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
        $count = $this->db->exercises_subject()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->exercises_subject()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $exercises_subject_info = $this->db->exercises_subject()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($exercises_subject_info) {
            $exercises_subject_info = iterator_to_array($exercises_subject_info->getIterator());
            $exercises_subject_info['choice'] = empty($exercises_subject_info['choice']) ? $exercises_subject_info['choice'] : json_decode($exercises_subject_info['choice'], true);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $exercises_subject_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "题目不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}