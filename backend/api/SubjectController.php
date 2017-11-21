<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SubjectController
 * @package backend\api
 */
class SubjectController extends Controller
{
    const SUBJECT_ONE = 1;//单选
    const SUBJECT_TWO = 2;//多选
    const SUBJECT_THREE = 3;//填空
    const SUBJECT_FOUR = 4;//处方审核
    const SUBJECT_FIVE = 5;//用药交代
    const SUBJECT_SIX = 6;//问答
    const SUBJECT_SEVEN = 7;//材料

    const PURPOSE_ONE = 1; //收藏
    const PURPOSE_TWO = 2; //错题
    const PURPOSE_THREE = 3; //查看解析

    const PRICE_PURPOSE_THREE_NO = 1; //查看解析不收费
    const PRICE_PURPOSE_THREE_YES = 2; //查看解析收费

    private $_limit = 10;
    private $_search_black = [
        'page','order'
    ];

    /* add */
    public function actionAdd(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $public_form = ['questions_id','right_key','score','role','degree','pharmacy_id','analysis','price','is_price'];
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        if ($post['topic_type'] == self::SUBJECT_ONE || $post['topic_type'] == self::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
                'create_time' => time(),
            ];
            $this->db->transaction = 'BEGIN';
            $result = $this->db->subject()->insert($insert_row);
            $result_subject_id = $this->db->subject()->insert_id();
            $result_questions_subject = $this->db->questions_subject()->insert(['questions_id'=>$post['questions_id'], 'subject_id'=>$result_subject_id, 'create_time'=>time()]);
            if ($result && $result_questions_subject) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else if ($post['topic_type'] == self::SUBJECT_FOUR || $post['topic_type'] == self::SUBJECT_FIVE) {
            //处方 || 用药交代
            if (!$this->formControlEmpty(array_merge(['photo'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
                'create_time' => time(),
            ];

            //处方图片处理
            $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject', 'subject_prescription');
            if (!empty($uploadResult)) {
                //上传新图片
                $insert_row['real_photo'] = $uploadResult['realPath'];
                $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
            $this->db->transaction = 'BEGIN';
            $result_subject = $this->db->subject()->insert($insert_row);
            $result_subject_id = $this->db->subject()->insert_id();
            //患者信息提取
            $insert_patient = [
                'name' => $post['patient_name'] ? $post['patient_name'] : 'name_'.$result_subject_id,
                'subject_id' => $result_subject_id,
                'sex' => $post['sex'] ? $post['sex'] : 1,
                'age' => $post['age'] ? $post['age'] : 0,
                'department' => $post['department'] ? $post['department'] : '',
                'clinical_diagnosis' => $post['clinical_diagnosis'] ? $post['clinical_diagnosis'] : '',
                'medicine' => $post['medicine'] ? $post['medicine'] : '',
                'create_time' => time()
            ];
            $result_patient = $this->db->patient()->insert($insert_patient);
            $result_questions_subject = $this->db->questions_subject()->insert(['questions_id'=>$post['questions_id'], 'subject_id'=>$result_subject_id, 'create_time'=>time()]);
            if ($result_subject && $result_patient && $result_questions_subject) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else if ($post['topic_type'] == self::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $insert_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
                'create_time' => time(),
            ];
            $this->db->transaction = 'BEGIN';
            $result = $this->db->subject()->insert($insert_row);
            $result_subject_id = $this->db->subject()->insert_id();
            $result_questions_subject = $this->db->questions_subject()->insert(['questions_id'=>$post['questions_id'], 'subject_id'=>$result_subject_id, 'create_time'=>time()]);
            if ($result && $result_questions_subject) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }

        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
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

        $questions_id = $this->db->subject()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('id');
        if (empty($questions_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'账户不存在']));
        }
        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->subject()->where(['id' => $post['id']])->update($update_row);
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
        $this->db->transaction = 'BEGIN';
        $result = $this->db->subject()->where(['id' => $post['id']])->update($update_row);
        //多对多删除 ys_questions_subject
        $check = $this->iterator_array($this->db->questions_subject()->select('')->where(['subject_id' => $post['id']])->limit(1));
        $result_delete = true;
        if (!empty($check)) {
            $result_delete = $this->db->questions_subject()->where(['subject_id' => $post['id']])->delete();
        }
        if ($result && $result_delete) {
            $this->db->transaction = 'COMMIT';
            $return = ['status' => true, 'message' => '操作成功'];
        } else {
            $this->db->transaction = 'ROLLBACK';
            $return = ['status' => false, 'message' => '操作失败'];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* update */
    public function actionUpdate(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['id', 'topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $subject_info = $this->db->subject()
            ->where(['id'=>$post['id'], 'status'=>1])->fetch();
        if (!$subject_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'非法请求']));
        }
        $subject_info = iterator_to_array($subject_info->getIterator());
        $public_form = ['right_key','score','role','degree','pharmacy_id','analysis','price','is_price'];
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        if ($post['topic_type'] == self::SUBJECT_ONE || $post['topic_type'] == self::SUBJECT_TWO) {
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
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
            ];
            $result = $this->db->subject()->where(['id'=>$post['id']])->update($update_row);
        } else if ($post['topic_type'] == self::SUBJECT_FOUR || $post['topic_type'] == self::SUBJECT_FIVE) {
            //处方 || 用药交代
            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
            ];

            //处方图片处理
            if (!empty($post['photo'])) {
                $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject', 'subject_prescription');
                if (!empty($uploadResult)) {
                    //删除
                    @unlink($subject_info['real_photo']);
                    //上传新图片
                    $insert_row['real_photo'] = $uploadResult['realPath'];
                    $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
                }
            }

            $this->db->transaction = 'BEGIN';
            $result_subject = $this->db->subject()->where(['id'=>$post['id']])->update($update_row);
            //删除对应的患者信息
            $result_delete = $this->db->patient()->where(['subject_id'=>$post['id']])->delete();
            //患者信息提取
            $insert_patient = [
                'name' => $post['patient_name'] ? $post['patient_name'] : 'name_'.$post['id'],
                'subject_id' => $post['id'],
                'sex' => $post['sex'] ? $post['sex'] : 1,
                'age' => $post['age'] ? $post['age'] : 0,
                'department' => $post['department'] ? $post['department'] : '',
                'clinical_diagnosis' => $post['clinical_diagnosis'] ? $post['clinical_diagnosis'] : '',
                'medicine' => $post['medicine'] ? $post['medicine'] : '',
                'create_time' => time()
            ];
            $result_patient = $this->db->patient()->insert($insert_patient);
            if ($result_subject && $result_patient && $result_delete) {
                $this->db->transaction = 'COMMIT';
                $result = true;
            } else {
                $this->db->transaction = 'ROLLBACK';
                $result = false;
            }
        } else if ($post['topic_type'] == self::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }

            $update_row = [
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 1,
                'degree' => $post['degree'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'price' => $post['price'],
                'is_price' => $post['is_price'],
            ];
            $result = $this->db->subject()->where(['id'=>$post['id']])->update($update_row);
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
        unset($search['status']); //表中没有该字段
        $count = $this->db->subject()->select('*')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->subject()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $subject_info = $this->db->subject()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($subject_info) {
            $subject_info = iterator_to_array($subject_info->getIterator());
            $subject_info['choice'] = json_decode($subject_info['choice'], true);
            //获取该题目下的患者信息
            $patient_info = $this->db->patient()->where(['subject_id' => $subject_info['id'], 'status'=>[1,2]])->fetch();
            $subject_info['patient_info'] = $patient_info ? iterator_to_array($patient_info->getIterator()) : [];
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
}