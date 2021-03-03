<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\CircleModule;
use Dolphin\Ting\Http\Modules\FileModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CircleService extends Service
{
    private $validation;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
    }

    /**
     * 添加圈子动态数据
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function add(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'content' => v::optional(v::notEmpty()),
            'images'  => v::optional(v::stringVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $content = isset($params['content']) ? $params['content'] : '';
        $images = isset($params['images']) ? $params['images'] : '';
        $data = CircleModule::getInstance($this->container)->add(1, $content, $images);
        return new ServiceResponse($data);
    }

    /**
     * 获取圈子动态数据列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function getList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::intVal()),
            'limit'  => v::optional(v::intVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 10;
        $data = CircleModule::getInstance($this->container)->getList($start, $limit);
        return new ServiceResponse($data);
    }
}