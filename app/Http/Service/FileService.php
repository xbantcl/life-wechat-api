<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\FileModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class FileService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    /**
     * 获取七牛文件上传token
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getUploadToken(Request $request, Response $response)
    {
        $data = FileModule::getInstance($this->container)->getUploadToken();
        return new ServiceResponse($data);
    }
}