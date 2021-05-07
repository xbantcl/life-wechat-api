<?php

namespace Dolphin\Ting\Bootstrap\Middleware;

use DI\Container;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Fig\Http\Message\RequestMethodInterface;

class AuthMiddleware implements MiddlewareInterface, RequestMethodInterface
{
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
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
        $uri = $request->getRequestTarget();
        if (!empty($params['token'])) {
            $payload = Help::decode($params['token']);
            if ($payload === false && $uri !== '/api/circle/list') {
                return new ServiceResponse([], -2, '请输入有效token');
            }
            $this->container->set('uid', $payload['uid']);
        } else {
            if ($uri !== '/api/circle/list') {
                return new ServiceResponse([], -2, '请输入有效token');
            }
        }
        $response = $handler->handle($request);
        return $response;
    }
}