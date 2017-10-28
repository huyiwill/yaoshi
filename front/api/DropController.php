<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class DropController
 * @package front\api
 */
class DropController extends Controller
{
    public function actionQuestions(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->admin_questions()->where(['status'=>1])->order('id asc')->select('id','name'));
        if ($result) {
            $return = [
                'status' => true,
                'data' => $result,
                'message' => '获取成功'
            ];
        } else {
            $return = [
                'status' => false,
                'message' => '数据错误'
            ];
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode($return));
    }
}