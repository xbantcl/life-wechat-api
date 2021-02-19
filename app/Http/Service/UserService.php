<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Model\UserModel;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class UserService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    public function getUserInfo(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::numeric()),
            'limit' => v::optional(v::numeric())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }

        return new ServiceResponse([1,2,3]);
    }
}