<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\RentModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 回收相关接口类
class RecycleService extends Service
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
     * 回收预约
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'type' => v::in([1, 2])->notEmpty(),
            'price' => v::notEmpty(),
            'mobile' => v::numericVal(),
            'title' => v::notEmpty(),
            'category' => v::in(['手动工具', '电动工具', '运输工具', '其他工具'])->notEmpty(),
            'address' => v::notEmpty(),
            'lat' => v::notEmpty(),
            'lng' => v::notEmpty(),
            'desc' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $type = isset($params['type']) ? intval($params['type']) : 1;
        $price = isset($params['price']) ? trim($params['price']) : '免费';
        $mobile = isset($params['mobile']) ? trim($params['mobile']) : '';
        $title = isset($params['title']) ? $params['title']: '';
        $category = isset($params['category']) ? trim($params['category']) : '手动工具';
        $address = isset($params['address']) ? trim($params['address']) : '南湖世纪';
        $lat = isset($params['lat']) ? trim($params['lat']) : 1;
        $lng = isset($params['lng']) ? trim($params['lng']) : '';
        $desc = isset($params['desc']) ? trim($params['desc']) : '';
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = RentModule::getInstance($this->container)->add($this->uid, $type, $price, $mobile, $title, $category, $address, $lat, $lng, $desc, $images);
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
            'type' => v::in([1, 2])->notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $type = isset($params['type']) ? $params['type'] : 1;
        $title = isset($params['title']) ? trim($params['title']) : '';
        $isPullDown = isset($params['is_pull_down']) ? boolval($params['is_pull_down']) : false;
        $lat = isset($params['lat']) ? trim($params['lat']) : '';
        $lng = isset($params['lng']) ? trim($params['lng']) : '';
        $data = RentModule::getInstance($this->container)->getList($start, $type, $title, $isPullDown, $limit, $lat, $lng);
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
        $id = isset($params['id']) ? intval($params['id']) : 0;
        $data = CarPlaceModule::getInstance($this->container)->deleteComment($this->uid, $id);
        return new ServiceResponse($data);
    }
}