<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class TestSubjectController
 * @package front\api
 */
class TestSubjectController extends Controller
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
        if (!$this->formControlEmpty(['topic_type','test_id','questions_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!$this->_checkUserTest($post['test_id'], $cache_user_info['id'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'试卷不存在']));
        }
        $questions_id = $this->db->admin_questions()->where(['id'=>$post['questions_id']])->fetch('id');
        if (empty($questions_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题库不存在']));
        }
        $public_form = ['name','right_key','score','role','pharmacy_id','analysis'];
        $current_order = $this->iterator_array($this->db->test_subject()->select('Max(sort) as sort')->where(['test_id'=>$post['test_id']]));
        $current_order = $current_order[0]['sort'] ? $current_order[0]['sort'] : 0;
        //加入用户题目审核列表
        $this->db->transaction = 'BEGIN';
        //category_id
        $category_id = $this->getTopCategory($post['pharmacy_id']);
        if ($post['topic_type'] == SubjectController::SUBJECT_ONE || $post['topic_type'] == SubjectController::SUBJECT_TWO) {
            //单选 || 双选
            if (!$this->formControlEmpty(array_merge(['choice'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'test_id' => $post['test_id'],
                'name' => $post['name'],
                'choice' => json_encode($post['choice']),
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 0,
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];
            $insert_examine = [
                'uid' => $cache_user_info['id'],
                'questions_id' => $post['questions_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'choice' => json_encode($post['choice']),
                'right_key' => $post['right_key'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'create_time' => time()
            ];
            $result = $this->db->test_subject()->insert($insert_row);
            $result_examine = $this->db->subject_examine()->insert($insert_examine);
            if ($result !== false && $result_examine !== false) {
                $return = true;
                $this->db->transaction = 'COMMIT';
            } else {
                $return = false;
                $this->db->transaction = 'ROLLBACK';
            }
        } else if ($post['topic_type'] == SubjectController::SUBJECT_FOUR || $post['topic_type'] == SubjectController::SUBJECT_FIVE) {
            //处方 || 用药交代
            if (!$this->formControlEmpty(array_merge(['photo'], $public_form), $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'test_id' => $post['test_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 0,
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];
            $insert_examine = [
                'uid' => $cache_user_info['id'],
                'questions_id' => $post['questions_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'create_time' => time()
            ];
            //处方图片处理
            $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject', 'subject_prescription');
            if (!empty($uploadResult)) {
                //上传新图片
                $insert_row['real_photo'] = $uploadResult['realPath'];
                $insert_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
                $insert_examine['real_photo'] = $uploadResult['realPath'];
                $insert_examine['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
            }
            $result = $this->db->test_subject()->insert($insert_row);
            $result_examine = $this->db->subject_examine()->insert($insert_examine);
            if ($result !== false && $result_examine !== false) {
                $return = true;
                $this->db->transaction = 'COMMIT';
            } else {
                $return = false;
                $this->db->transaction = 'ROLLBACK';
            }
        } else if ($post['topic_type'] == SubjectController::SUBJECT_SIX) {
            //问答
            if (!$this->formControlEmpty($public_form, $post)) {
                return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
            }
            $insert_row = [
                'test_id' => $post['test_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'score' => $post['score'],
                'role' => $post['role'],
                'post' => $post['post'] ? $post['post'] : 0,
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'therapeutic' => $post['therapeutic'] ? $post['therapeutic'] : '',
                'analysis' => $post['analysis'],
                'sort' => $current_order+1,
                'create_time' => time(),
            ];
            $insert_examine = [
                'uid' => $cache_user_info['id'],
                'questions_id' => $post['questions_id'],
                'name' => $post['name'],
                'topic_type' => $post['topic_type'],
                'right_key' => $post['right_key'],
                'category_id' => $category_id,
                'pharmacy_id' => $post['pharmacy_id'],
                'create_time' => time()
            ];
            $result = $this->db->test_subject()->insert($insert_row);
            $result_examine = $this->db->subject_examine()->insert($insert_examine);
            if ($result !== false && $result_examine !== false) {
                $return = true;
                $this->db->transaction = 'COMMIT';
            } else {
                $return = false;
                $this->db->transaction = 'ROLLBACK';
            }
        } else {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题型错误']));
        }

        $return = $return ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* choice */
    public function actionChoice(Request $request, Response $response)
    {
        $post = $request->getParsedBody();
        //form control empty
        if (!$this->formControlEmpty(['subjects','test_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!$this->_checkUserTest($post['test_id'], $cache_user_info['id'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不存在试卷']));
        }
        $current_order = $this->iterator_array($this->db->test_subject()->select('Max(sort) as sort')->where(['test_id'=>$post['test_id']]));
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
                        'test_id' => $post['test_id'],
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
                    $result = $this->db->test_subject()->insert($insert_row);
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
        $one_info = $this->db->test_subject()->where(['id'=>$post['change_one'],'status'=>1])->fetch();
        $two_info = $this->db->test_subject()->where(['id'=>$post['change_two'],'status'=>1])->fetch();
        if (empty($one_info) || empty($two_info)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'交换数据不存在']));
        }
        $one_info = iterator_to_array($one_info->getIterator());
        $two_info = iterator_to_array($two_info->getIterator());
        $this->db->transaction = 'BEGIN';
        $one_result = $this->db->test_subject()->where(['id'=>$one_info['id']])->update(['sort'=>$two_info['sort']]);
        $two_result = $this->db->test_subject()->where(['id'=>$two_info['id']])->update(['sort'=>$one_info['sort']]);
        if ($one_result !== false && $two_result !== false) {
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

        $test_subject_test_id = $this->db->test_subject()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('test_id');
        if (empty($test_subject_test_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!$this->_checkUserTest($test_subject_test_id, $cache_user_info['id'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不存在试卷']));
        }

        $update_row = [
            'status' => $post['status']
        ];
        $result = $this->db->test_subject()->where(['id' => $post['id']])->update($update_row);
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
        $test_subject_test_id = $this->db->test_subject()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('test_id');
        if (empty($test_subject_test_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!$this->_checkUserTest($test_subject_test_id, $cache_user_info['id'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不存在试卷']));
        }

        $update_row = [
            'status' => 3
        ];
        $result = $this->db->test_subject()->where(['id' => $post['id']])->update($update_row);
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
        if (!$this->formControlEmpty(['id','topic_type'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }
        $test_subject_test_id = $this->db->test_subject()->where(['id' => $post['id'], 'status'=>[1,2]])->fetch('test_id');
        if (empty($test_subject_test_id)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'题目不存在']));
        }

        $cache_user_info = $this->getLogin('_ys_front_login', $request);
        if (!$this->_checkUserTest($test_subject_test_id, $cache_user_info['id'])) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'不存在试卷']));
        }
        $public_form = ['name','right_key','score','pharmacy_id'];

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
            $result = $this->db->test_subject()->where(['id'=>$post['id']])->update($update_row);
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
                $uploadResult = $this->uploadBase64Image($post['photo'], '', 'subject', 'subject_prescription');
                if (!empty($uploadResult)) {
                    //删除旧图片
                    $test_subject_info = $this->db->test_subject()->where(['id'=>$post['id']])->fetch();
                    if ($test_subject_info) {
                        $test_subject_info = iterator_to_array($test_subject_info->getIterator());
                        @unlink($test_subject_info['real_photo']);
                    }
                    //上传新图片
                    $update_row['real_photo'] = $uploadResult['realPath'];
                    $update_row['photo'] = $_SERVER['REQUEST_SCHEME'] .'://'. $_SERVER['HTTP_HOST'] .$uploadResult['photo'];
                }
            }
            $result = $this->db->test_subject()->where(['id'=>$post['id']])->update($update_row);
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
            $result = $this->db->test_subject()->where(['id'=>$post['id']])->update($update_row);
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
        $count = $this->db->test_subject()->select('')->where($search)->count();
        $number = ceil($count / $limit);
        $result = $this->db->test_subject()->select('')->where($search)->order($order)->limit($limit, ($page - 1) * $limit);
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

        $test_subject_info = $this->db->test_subject()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($test_subject_info) {
            $test_subject_info = iterator_to_array($test_subject_info->getIterator());
            $test_subject_info['choice'] = empty($test_subject_info['choice']) ? $test_subject_info['choice'] : json_decode($test_subject_info['choice'], true);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $test_subject_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "题目不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    private function _checkUserTest($test_id,$uid)
    {
        $test_id = $this->db->test()->where(['id'=>$test_id,'uid_admin'=>$uid,'status'=>1])->fetch();
        if (empty($test_id)) {
            return false;
        }
        return true;
    }
}