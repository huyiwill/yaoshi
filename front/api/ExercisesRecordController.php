<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * 历年真题答题记录
 * Class ExercisesRecordController
 * @package front\api
 */
class ExercisesRecordController extends Controller
{
    /* 查看答案 */
    public function actionInfo(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $exercises_record_info = $this->db->exercises_record()->where(['id' => $get['id'], 'status'=>[1,2]])->fetch();
        if ($exercises_record_info) {
            $exercises_record_info = iterator_to_array($exercises_record_info->getIterator());
            $exercises_record_info['exercises_subject'] = json_decode($exercises_record_info['exercises_subject'], true);
            $return = [
                'status' => true,
                'message' => '',
                'data' => $exercises_record_info
            ];
        } else {
            $return = [
                'status' => false,
                'message' => "答案不存在"
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}