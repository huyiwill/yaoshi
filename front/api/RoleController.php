<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class RoleController
 * @package front\api
 */
class RoleController extends Controller
{
    public function actionDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->role()->where(['status'=>1])->order('id asc')->select('id,name'));
        if (!empty($result)) {
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