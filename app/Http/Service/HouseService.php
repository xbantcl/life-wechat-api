<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\HouseModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 房屋相关接口类
class HouseService extends Service
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
     * 发布房屋信息
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'type' => v::in(['出租', '出售'])->notEmpty(),
            'price' => v::floatVal(),
            'elevator' => v::in(['有', '无'])->notEmpty(),
            'floorage' => v::floatVal(),
            'floor' => v::notEmpty(),
            'subdistrict' => v::notEmpty(),
            'direction' => v::notEmpty(),
            'decorate' => v::notEmpty(),
            'house_type' => v::notEmpty(),
            'house_layout' => v::notEmpty(),
            'mobile' => v::numericVal(),
            'describe' => v::notEmpty(),
            'images' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $type = trim($params['type']);
        $price = floatval($params['price']);
        $elevator = trim($params['elevator']);
        $floorage = floatval($params['floorage']);
        $floor = trim($params['floor']);
        $subdistrict = trim($params['subdistrict']);
        $mobile = trim($params['mobile']);
        $direction = trim($params['direction']);
        $houseType = trim($params['house_type']);
        $houseLayout = trim($params['house_layout']);
        $describe = trim($params['describe']);
        $decorate = trim($params['decorate']);
        $images = trim($params['images']);
        $data = HouseModule::getInstance($this->container)->add($this->uid, $type, $price, $elevator,
            $floorage, $floor, $subdistrict, $houseLayout, $houseType, $direction, $decorate, $describe, $mobile, $images);
        return new ServiceResponse($data);
    }

    /**
     * 获取房子列表
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
        $data = HouseModule::getInstance($this->container)->getList($start, $type, $isPullDown, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取用户房屋数据列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function getListByUid(Request $request, Response $response)
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
        $data = HouseModule::getInstance($this->container)->getListByUid($this->uid, $start, $limit);
        return new ServiceResponse($data);
    }
    
    /**
     * 获取房子详情
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
        $data = HouseModule::getInstance($this->container)->detail($id);
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

    /**
     * 更改数据状态
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function changeStatus(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id'  => v::intVal(),
            'status' => v::in([1, 2, 3])
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $status = intval($params['status']);
        $data = HouseModule::getInstance($this->container)->changeStatus($this->uid, $id, $status);
        return new ServiceResponse($data);
    }
}