<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class SubjectController
 * @package front\api
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
        'page', 'order'
    ];

    /* 专项 one two list by category */
    public function actionListByCategory(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['pharmacy_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }
        $current_user_info = $this->currentUserInfo('_ys_front_login', $request);
        //单选题 数量 && 数据
        $one_count = $this->db->subject()->select('')->where(['pharmacy_id' => $post['pharmacy_id'], 'topic_type' => self::SUBJECT_ONE, 'role' => $current_user_info['role'], 'status' => 1])->count();
        $one_result = $this->iterator_array($this->db->subject()->select('id, name, topic_type, choice, right_key, score, degree')
            ->where(['pharmacy_id' => $post['pharmacy_id'], 'topic_type' => self::SUBJECT_ONE, 'role' => $current_user_info['role'], 'status' => 1]));
        //多选题 数量 && 数据
        $two_count = $this->db->subject()->where(['pharmacy_id' => $post['pharmacy_id'], 'topic_type' => self::SUBJECT_TWO, 'role' => $current_user_info['role'], 'status' => 1])->count();
        $two_result = $this->iterator_array($this->db->subject()->select('id, name, topic_type, choice, right_key, score, degree')
            ->where(['pharmacy_id' => $post['pharmacy_id'], 'topic_type' => self::SUBJECT_TWO, 'role' => $current_user_info['role'], 'status' => 1]));

        if (!empty($one_result)) {
            array_walk($one_result, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }

        if (!empty($two_result)) {
            array_walk($two_result, function (&$item) {
                $item['choice'] = json_decode($item['choice'], true);
            });
        }

        $return = [
            'status' => true,
            'data' => [
                'one_count' => $one_count,
                'one_result' => $one_result,
                'two_count' => $two_count,
                'two_result' => $two_result,
            ]
        ];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 纠错 */
    public function actionCheckError(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id', 'error'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        $insert_row = [
            'uid' => $user_cache_info['id'],
            'subject_id' => $post['subject_id'],
            'error' => $post['error'],
            'create_time' => time()
        ];
        $result = $this->db->subject_error()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 检测是否已收藏 */
    public function actionCheckPurposeOne(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //check repeat
        $purpose_one_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_ONE, 'status' => [1, 2]])->fetch('id');
        if ($purpose_one_id) {
            $return = [
                'status' => true,
                'data' => $purpose_one_id,
                'message' => '已收藏'
            ];
        } else {
            $return = [
                'status' => false,
                'message' => '未收藏'
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 收藏 */
    public function actionPurposeOne(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //check repeat
        $purpose_one_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_ONE, 'status' => [1, 2]])->fetch('id');
        if ($purpose_one_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '已收藏']));
        }

        $insert_row = [
            'uid' => $user_cache_info['id'],
            'subject_id' => $post['subject_id'],
            'purpose' => self::PURPOSE_ONE,
            'create_time' => time()
        ];
        $result = $this->db->subject_relation()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    public function actionCancelPurposeOne(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //check repeat
        $purpose_one_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_ONE, 'status' => [1, 2]])->fetch('id');
        if (!$purpose_one_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '未收藏']));
        }

        $result = $this->db->subject_relation()->where(['id' => $purpose_one_id, 'purpose' => self::PURPOSE_ONE])->delete();
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 查看解析 */
    public function actionPurposeThree(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }
        $subject_info = $this->db->subject()->where(['id' => $post['subject_id']])->fetch();
        if (!$subject_info) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '题目不存在']));
        }
        $subject_info = iterator_to_array($subject_info->getIterator());
        if ($subject_info['is_price'] == self::PRICE_PURPOSE_THREE_YES) {
            //收费
            $user_cache_info = $this->getLogin('_ys_front_login', $request);
            //check repeat
            $purpose_three_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_THREE, 'status' => [1, 2]])->fetch('id');
            if (MemberController::check($user_cache_info['id'])) {
                //会员
                //直接查看解析
                $return = [
                    'status' => true,
                    'data' => ['analysis' => $subject_info]
                ];
            } else if ($purpose_three_id) {
                //已购买
                //直接查看解析
                $return = [
                    'status' => true,
                    'data' => ['analysis' => $subject_info]
                ];
            } else {
                //需要积分购买
                $return = [
                    'status' => false,
                    'message' => '查看解析需' . $subject_info['price'] . '积分'
                ];
            }
        } else {
            //不收费
            //直接查看解析
            $return = [
                'status' => true,
                'data' => ['analysis' => $subject_info]
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 错题新增 */
    public function actionPurposeTwo(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //check repeat
        $purpose_two_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_TWO, 'status' => [1, 2]])->fetch('id');
        if ($purpose_two_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '已存在']));
        }

        $insert_row = [
            'uid' => $user_cache_info['id'],
            'subject_id' => $post['subject_id'],
            'purpose' => self::PURPOSE_TWO,
            'create_time' => time()
        ];
        $result = $this->db->subject_relation()->insert($insert_row);
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 收藏列表 */
    public function actionPurposeOneList(Request $request, Response $response)
    {
        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //exits ?
        $purpose_one_list = $this->iterator_array($this->db->subject_relation()->select('')->where(['uid' => $user_cache_info['id'], 'purpose' => self::PURPOSE_ONE, 'status' => 1])->limit(1));
        if (!empty($purpose_one_list)) {
            $sql = "SELECT s.id, s.`name`, s.topic_type, s.choice, s.right_key, s.score, s.degree FROM `ys_subject_relation` sr, `ys_subject` s
                  WHERE sr.subject_id = s.id AND sr.purpose = 1 AND sr.status = 1 AND sr.uid = :uid AND s.topic_type = :topic_type";
            //单选题 数量 && 数据
            $sth1 = $this->pdo->prepare($sql);
            $topic_type = self::SUBJECT_ONE;
            $sth1->bindParam(':topic_type', $topic_type, \PDO::PARAM_INT);
            $sth1->bindParam(':uid', $user_cache_info['id'], \PDO::PARAM_STR);
            $sth1->execute();
            $one_result = $sth1->fetchAll(\PDO::FETCH_ASSOC);
            $one_count = count($one_result);

            //多选题 数量 && 数据
            $sth2 = $this->pdo->prepare($sql);
            $topic_type = self::SUBJECT_TWO;
            $sth2->bindParam(':topic_type', $topic_type, \PDO::PARAM_INT);
            $sth2->bindParam(':uid', $user_cache_info['id'], \PDO::PARAM_STR);
            $sth2->execute();
            $two_result = $sth2->fetchAll(\PDO::FETCH_ASSOC);
            $two_count = count($two_result);

            if (!empty($one_result)) {
                array_walk($one_result, function (&$item) {
                    $item['choice'] = json_decode($item['choice'], true);
                });
            }

            if (!empty($two_result)) {
                array_walk($two_result, function (&$item) {
                    $item['choice'] = json_decode($item['choice'], true);
                });
            }

            $return = [
                'status' => true,
                'data' => [
                    'one_count' => $one_count,
                    'one_result' => $one_result,
                    'two_count' => $two_count,
                    'two_result' => $two_result,
                ]
            ];
        } else {
            $return = [
                'status' => true,
                'data' => []
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 错题列表 */
    public function actionPurposeTwoList(Request $request, Response $response)
    {
        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //exits ?
        $purpose_two_list = $this->iterator_array($this->db->subject_relation()->select('')->where(['uid' => $user_cache_info['id'], 'purpose' => self::PURPOSE_TWO, 'status' => 1])->limit(1));
        if (!empty($purpose_two_list)) {
            $sql = "SELECT s.id, s.`name`, s.topic_type, s.choice, s.right_key, s.score, s.degree FROM `ys_subject_relation` sr, `ys_subject` s
                  WHERE sr.subject_id = s.id AND sr.purpose = 2 AND sr.status = 1 AND sr.uid = :uid AND s.topic_type = :topic_type";
            //单选题 数量 && 数据
            $sth1 = $this->pdo->prepare($sql);
            $topic_type = self::SUBJECT_ONE;
            $sth1->bindParam(':topic_type', $topic_type, \PDO::PARAM_INT);
            $sth1->bindParam(':uid', $user_cache_info['id'], \PDO::PARAM_STR);
            $sth1->execute();
            $one_result = $sth1->fetchAll(\PDO::FETCH_ASSOC);
            $one_count = count($one_result);

            //多选题 数量 && 数据
            $sth2 = $this->pdo->prepare($sql);
            $topic_type = self::SUBJECT_TWO;
            $sth2->bindParam(':topic_type', $topic_type, \PDO::PARAM_INT);
            $sth2->bindParam(':uid', $user_cache_info['id'], \PDO::PARAM_STR);
            $sth2->execute();
            $two_result = $sth2->fetchAll(\PDO::FETCH_ASSOC);
            $two_count = count($two_result);

            if (!empty($one_result)) {
                array_walk($one_result, function (&$item) {
                    $item['choice'] = json_decode($item['choice'], true);
                });
            }

            if (!empty($two_result)) {
                array_walk($two_result, function (&$item) {
                    $item['choice'] = json_decode($item['choice'], true);
                });
            }

            $return = [
                'status' => true,
                'data' => [
                    'one_count' => $one_count,
                    'one_result' => $one_result,
                    'two_count' => $two_count,
                    'two_result' => $two_result,
                ]
            ];
        } else {
            $return = [
                'status' => true,
                'data' => []
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }

    /* 答对删除错题 */
    public function actionCancelPurposeTwo(Request $request, Response $response)
    {
        $post = $request->getParsedBody();

        //form control empty
        if (!$this->formControlEmpty(['subject_id'], $post)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '必填参数错误']));
        }

        $user_cache_info = $this->getLogin('_ys_front_login', $request);
        //check repeat
        $purpose_two_id = $this->db->subject_relation()->where(['uid' => $user_cache_info['id'], 'subject_id' => $post['subject_id'], 'purpose' => self::PURPOSE_TWO, 'status' => [1, 2]])->fetch('id');
        if (!$purpose_two_id) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status' => false, 'message' => '未答错']));
        }

        $result = $this->db->subject_relation()->where(['id' => $purpose_two_id, 'purpose' => self::PURPOSE_TWO])->delete();
        $return = $result ? ['status' => true, 'message' => '操作成功'] : ['status' => false, 'message' => '操作失败'];
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}