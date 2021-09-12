<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\ProductModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class ProductService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    /**
     * 获取商品分类
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getCategories(Request $request, Response $response)
    {
        $data = ProductModule::getInstance($this->container)->getCategories();
        return new ServiceResponse($data);
    }
}