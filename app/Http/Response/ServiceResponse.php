<?php
// 用户信息
namespace Dolphin\Ting\Http\Response;

use Fig\Http\Message\StatusCodeInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Headers;
use Slim\Psr7\Response;

class ServiceResponse extends Response
{
    protected $data = [];

    public function __construct($data = [], $code = 0, $note = 'success')
    {
        $header = new Headers();
        $header->addHeader('Content-Type', 'application/json');

        $responseFactory = new ResponseFactory();
        $response        = $responseFactory->createResponse();
        $responseBody    = $response->getBody();
        $responseBody->write(json_encode([
            'code' => $code,
            'note' => $note,
            'data' => $data
        ]));
        $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Content-Type', 'application/json');

        parent::__construct(StatusCodeInterface::STATUS_OK, $header, $responseBody);
    }
}