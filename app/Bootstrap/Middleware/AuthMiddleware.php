<?php

namespace Dolphin\Ting\Bootstrap\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;

class AuthMiddleware implements MiddlewareInterface, RequestMethodInterface
{
    /**
     * Json 中间件
     *
     * @param  Request        $request PSR-7  request
     * @param  RequestHandler $handler PSR-15 request handler
     *
     * @return Response
     */
    public function process(Request $request, RequestHandler $handler): Response
    {
        // 请求类型
        $params = $request->getParsedBody();
        
        $response = $handler->handle($request);

        return $response;
    }
}