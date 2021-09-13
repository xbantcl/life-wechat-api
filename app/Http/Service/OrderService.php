<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\OrderModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class OrderService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    /**
     * 获取订单列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getOrderList(Request $request, Response $response)
    {
        $data = OrderModule::getInstance($this->container)->getOrderList();
        return new ServiceResponse($data);
    }

    /**
     * 获取订单详情
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getOrderDetail(Request $request, Response $response)
    {
        $data = OrderModule::getInstance($this->container)->getOrderDetail();
        return new ServiceResponse($data);
    }
}