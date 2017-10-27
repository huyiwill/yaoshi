<?php

namespace front\middleware;

use  \Psr\Container\ContainerInterface;
class TestMiddleware
{
    private $ci;

    public function __construct(ContainerInterface $ci)
    {
        $this->ci = $ci;
    }

    /**
     * Test middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
//        var_dump($request->getAttribute('route')->getName());exit;
        $response = $next($request, $response);
        return $response;
    }
}