<?php

namespace front\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use PHPQRCode\Constants;
use PHPQRCode\QRcode;

/**
 * 二维码
 * Class QrController
 * @package front\api
 */
class QrController extends Controller
{
    public function actionUrlCode(Request $request, Response $response, $args)
    {
        $url = $args['url'];
        QRcode::png($url,false, Constants::QR_ECLEVEL_L,10,3, true);
    }
}