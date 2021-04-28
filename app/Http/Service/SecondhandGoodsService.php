<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\CarPlaceModule;
use Dolphin\Ting\Http\Modules\SecondhandGoodsModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 车位相关接口类
class SecondhandGoodsService extends Service
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
     * 发布商品信息
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'title' => v::notEmpty(),
            'category' => v::in(['数码产品', '家用电器', '儿童玩具', '家居用品', '其他物品'])->notEmpty(),
            'price' => v::floatVal(),
            'original_price' => v::floatVal(),
            'address' => v::notEmpty(),
            'delivery' => v::in(['自取', '包邮'])->notEmpty(),
            'describe' => v::notEmpty(),
            'images' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $title = isset($params['title']) ? trim($params['title']) : '';
        $category = isset($params['category']) ? trim($params['category']) : '数码产品';
        $price = isset($params['price']) ? floatval($params['price']) : 0;
        $originalPrice = isset($params['original_price']) ? floatval($params['original_price']) : 0;
        $address = isset($params['address']) ? trim($params['address']) : '';
        $delivery = isset($params['delivery']) ? trim($params['delivery']) : '自取';
        $describe = isset($params['describe']) ? trim($params['describe']) : '';
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = SecondhandGoodsModule::getInstance($this->container)->add($this->uid, $title, $price, $originalPrice, $address, $describe, $delivery, $images, $category);
        return new ServiceResponse($data);
    }

    /**
     * 获取商品列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'category' => v::in(['all', '数码产品', '家用电器', '儿童玩具', '家居用品', '其他物品'])->notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $category = isset($params['category']) ? trim($params['category']) : 'all';
        $isPullDown = isset($params['is_pull_down']) ? boolval($params['is_pull_down']) : false;
        $data = SecondhandGoodsModule::getInstance($this->container)->getList($start, $category, $isPullDown, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取用户商品列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getListByUid (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'target_uid' => v::optional(v::numericVal()),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $targetUid = isset($params['target_uid']) ? intval($params['target_uid']) : 0;
        if ($targetUid > 0) {
            $uid = $targetUid;
        } else {
            $uid = $this->uid;
        }
        $data = SecondhandGoodsModule::getInstance($this->container)->getListByUid($uid, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取商品详情
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function detail (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = isset($params['id']) ? intval($params['id']) : 0;
        $data = SecondhandGoodsModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }
}