<?php

namespace backend\api;

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

/**
 * Class RoleController
 * @package backend\api
 */
class ErrorController extends Controller
{

    public static function notFound(Request $request, Response $response)
    {
        return $response->withRedirect('/404.html');
    }
}