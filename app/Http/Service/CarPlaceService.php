<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Response\ServiceResponse;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 车位相关接口类
class CarPlaceService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    public function addCarPlace (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
    }

    public function getCarList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }

        return new ServiceResponse([1,2,3]);
    }
}