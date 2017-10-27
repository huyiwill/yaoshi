<?php

namespace front\middleware;

use  \Psr\Container\ContainerInterface;
class LoginMiddleware
{
    private $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * Login middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $current_cookie = $request->getCookieParams();
        if (isset($current_cookie['_front']) && strlen($current_cookie['_front']) == 32) {
            $cookie_id = $current_cookie['_front'];
        } else {
            $cookie_id = md5(uniqid() . uniqid());
        }
        $login_id = 'ys_front_' . $cookie_id;
        $result = $this->ci->get('redis')->get($login_id);
        $data = json_decode($result, true);
        if (isset($data['_ys_front_login'])) {
            $response = $next($request, $response);
            return $response;
        }
        return $response->withHeader('Content-type', 'application/json')->write(json_encode(['status'=>false,'message'=>'请先登录']));
    }
}