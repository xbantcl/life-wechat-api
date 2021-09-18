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

    /**
     * 添加商品信息
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function addProduct(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'category_id' => v::intVal()->notEmpty(),
            'labels' => v::notEmpty(),
            'price' => v::floatVal()->notEmpty(),
            'sort' => v::intVal()->notEmpty(),
            'description' => v::notEmpty(),
            'images' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $categoryId = intval($params['category_id']);
        $materials = isset($params['materials']) ? trim($params['materials']) : '';
        $labels = trim($params['labels']);
        $price = floatval($params['price']);
        $sort = intval($params['sort']);
        $description = trim($params['description']);
        $images = trim($params['images']);
        $data = ProductModule::getInstance($this->container)->addProduct($name, $categoryId, $materials, $labels, $price, $sort, $description, $images);
        return new ServiceResponse($data);
    }

    /**
     * 添加商品标签
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function addLabel(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'category_id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $categoryId = intval($params['category_id']);
        $data = ProductModule::getInstance($this->container)->addLabel($name, $categoryId);
        return new ServiceResponse($data);
    }

    /**
     * 获取商品标签列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getLabelList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'category_id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $categoryId = intval($params['category_id']);
        $data = ProductModule::getInstance($this->container)->getLabelList($categoryId);
        return new ServiceResponse($data);
    }

    /**
     * 删除商品标签
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function deleteLabel(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = ProductModule::getInstance($this->container)->deleteLabel($id);
        return new ServiceResponse($data);
    }

    /**
     * 添加规格
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function addMaterial(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'category_id' => v::intVal()->notEmpty(),
            'params' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = trim($params['name']);
        $categoryId = intval($params['category_id']);
        $params = trim($params['params']);
        $data = ProductModule::getInstance($this->container)->addMaterial($name, $categoryId, $params);
        return new ServiceResponse($data);
    }

    /**
     * 获取商品规格列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getMaterialList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'category_id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $categoryId = intval($params['category_id']);
        $data = ProductModule::getInstance($this->container)->getMaterialList($categoryId);
        return new ServiceResponse($data);
    }

    /**
     * 删除商品规格
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function deleteMaterial(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = ProductModule::getInstance($this->container)->deleteMaterial($id);
        return new ServiceResponse($data);
    }
}