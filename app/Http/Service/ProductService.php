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

    /**
     * 添加商品分类
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function addCategory(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'image' => v::notEmpty(),
            'sort' => v::intVal()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $image = trim($params['image']);
        $sort = intval($params['sort']);
        $data = ProductModule::getInstance($this->container)->addCategory($name, $image, $sort);
        return new ServiceResponse($data);
    }

    /**
     * 获取商品分类列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getCategoryList(Request $request, Response $response)
    {
        $data = ProductModule::getInstance($this->container)->getCategoryList();
        return new ServiceResponse($data);
    }

    /**
     * 更新商品分类
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function updateCategory(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty(),
            'name' => v::notEmpty(),
            'sort' => v::intVal()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $name = trim($params['name']);
        $image = !empty(trim($params['image']))? trim($params['image']) : '';
        $sort = intval($params['sort']);
        $data = ProductModule::getInstance($this->container)->updateCategory($id, $name, $sort, $image);
        return new ServiceResponse($data);
    }

    /**
     * 删除商品分类
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function deleteCategory(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = ProductModule::getInstance($this->container)->deleteCategory($id);
        return new ServiceResponse($data);
    }
}