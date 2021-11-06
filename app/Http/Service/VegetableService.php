<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\VegetableModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 买菜相关接口类
class VegetableService extends Service
{
    private $validation;
    private $uid;

    public function __construct (Container $container)
    {
        parent::__construct($container);
        $this->validation = $container->get('validation');
        $this->uid = $container->get('uid');
    }

    /**
     * 添加菜品
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'desc' => v::notEmpty(),
            'price' => v::floatVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $desc = trim($params['desc']);
        $price = trim($params['price']);
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = VegetableModule::getInstance($this->container)->add($name, $price, $desc, $images);
        return new ServiceResponse($data);
    }

    /**
     * 更新菜品信息
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function update(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::notEmpty()->intVal(),
            'name' => v::notEmpty(),
            'price' => v::floatVal(),
            'desc' => v::notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $name = trim($params['name']);
        $desc = trim($params['desc']);
        $price = trim($params['price']);
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = VegetableModule::getInstance($this->container)->update($id, $name, $price, $desc, $images);
        return new ServiceResponse($data);
    }

    /**
     * 获取菜品列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'category_id' => v::optional(v::numericVal()),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $categoryId = isset($params['category_id']) ? intval($params['category_id']) : null;
        $start = isset($params['start']) ? trim($params['start']) : 0;
        $limit = isset($params['limit']) ? trim($params['limit']) : 10;
        $data = VegetableModule::getInstance($this->container)->getList($categoryId, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取菜品列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getTagList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $start = isset($params['start']) ? trim($params['start']) : 0;
        $limit = isset($params['limit']) ? trim($params['limit']) : 10;
        $data = VegetableModule::getInstance($this->container)->getTagList($start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取菜品详情
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function detail (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = VegetableModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }

    /**
     * 删除菜品
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id'  => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = VegetableModule::getInstance($this->container)->delete($id);
        return new ServiceResponse($data);
    }

    /**
     * 添加菜品分类
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function addCategory (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'vegetable_ids' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $vegetableIds = trim($params['vegetable_ids']);
        $data = VegetableModule::getInstance($this->container)->addCategory($name, $vegetableIds);
        return new ServiceResponse($data);
    }

    /**
     * 获取菜品分类列表
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function getCategoryList (Request $request, Response $response)
    {
        $data = VegetableModule::getInstance($this->container)->getCategoryList();
        return new ServiceResponse($data);
    }
}