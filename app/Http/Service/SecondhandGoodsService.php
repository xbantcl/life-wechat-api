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
     * 获取车位列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'type' => v::in(['all', '出售', '出租'])->notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $type = isset($params['type']) ? trim($params['type']) : 'all';
        $isPullDown = isset($params['is_pull_down']) ? boolval($params['is_pull_down']) : false;
        $data = CarPlaceModule::getInstance($this->container)->getList($start, $type, $isPullDown, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取车位详情
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
        $data = CarPlaceModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }

    /**
     * 发布车位评论
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function comment(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'reply_uid' => v::optional(v::intVal()),
            'car_place_id'  => v::intVal(),
            'content'  => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $replyUid = isset($params['reply_uid']) ? intval($params['reply_uid']) : 0;
        $carPlaceId = isset($params['car_place_id']) ? intval($params['car_place_id']) : 0;
        $data = CarPlaceModule::getInstance($this->container)->comment($this->uid, $replyUid, $carPlaceId, $params['content']);
        return new ServiceResponse($data);
    }

    /**
     * 获取车位评论列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function commentList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'car_place_id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $carPlaceId = isset($params['car_place_id']) ? intval($params['car_place_id']) : 0;
        $data = CarPlaceModule::getInstance($this->container)->commentList($carPlaceId);
        return new ServiceResponse($data);
    }

    /**
     * 删除车位评论
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function deleteComment(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = isset($params['']) ? intval($params['id']) : 0;
        $data = CarPlaceModule::getInstance($this->container)->deleteComment($this->uid, $id);
        return new ServiceResponse($data);
    }
}