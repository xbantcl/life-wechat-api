<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
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

    /**
     * 用户登录
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function login(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'phone' => v::notEmpty(),
            'password' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $data = UserModule::getInstance($this->container)->login(trim($params['phone']), trim($params['password']));
        return new ServiceResponse($data);
    }

    /**
     * 用户注册
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function register(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'phone' => v::notEmpty(),
            'password' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $data = UserModule::getInstance($this->container)->register(trim($params['phone']), trim($params['password']));
        return new ServiceResponse($data);
    }
}