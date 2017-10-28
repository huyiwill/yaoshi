<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class RegionController
 * @package backend\api
 */
class RegionController extends Controller
{
    /* 省 */
    public function actionProvinceDrop(Request $request, Response $response)
    {
        $result = $this->iterator_array($this->db->province()->where(['status'=>1])->order('id asc')->select('id,name'));
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

    /* 市 */
    public function actionCityDrop(Request $request, Response $response)
    {
        $get = $request->getQueryParams();
        //form control empty
        if (!$this->formControlEmpty(['province_id'], $get)) {
            return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'必填参数错误']));
        }

        $result = $this->iterator_array($this->db->city()->where(['pid'=>$get['province_id'],'status'=>1])->order('id asc')->select('id,name'));
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