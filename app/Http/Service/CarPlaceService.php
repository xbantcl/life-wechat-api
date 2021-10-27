<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\CarPlaceModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 车位相关接口类
class CarPlaceService extends Service
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
     * 发布车位信息
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
            'is_standard' => v::in([1, 2])->notEmpty(),
            'floorage' => v::floatVal(),
            'floor' => v::in(['负一楼', '负二楼', '地面'])->notEmpty(),
            'subdistrict' => v::notEmpty(),
            'building_num' => v::numericVal(),
            'mobile' => v::numericVal(),
            'describe' => v::notEmpty(),
            'weixin' => v::notEmpty(),
            'images' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $type = isset($params['type']) ? trim($params['type']) : '出售';
        $price = isset($params['price']) ? floatval($params['price']) : 0;
        $isStandard = isset($params['is_standard']) ? intval($params['is_standard']) : 1;
        $floorage = isset($params['floorage']) ? floatval($params['floorage']) : 0;
        $floor = isset($params['floor']) ? trim($params['floor']) : '负一楼';
        $subdistrict = isset($params['subdistrict']) ? trim($params['subdistrict']) : '南湖世纪';
        $buildingNum = isset($params['building_num']) ? trim($params['building_num']) : 1;
        $mobile = isset($params['mobile']) ? trim($params['mobile']) : '';
        $describe = isset($params['describe']) ? trim($params['describe']) : '';
        $weixin = isset($params['weixin']) ? trim($params['weixin']) : '';
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = CarPlaceModule::getInstance($this->container)->add($this->uid, $type, $price, $isStandard, $floorage,
            $floor, $subdistrict, $buildingNum, $describe, $mobile, $weixin, $images);
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
     * 获取车位数据列表
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
        $limit = isset($params['limit']) ? intval($params['limit']) : 6;
        $data = CarPlaceModule::getInstance($this->container)->getListByUid($this->uid, $start, $limit);
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

    /**
     * 删除车位
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = isset($params['id']) ? intval($params['id']) : 0;
        $data = CarPlaceModule::getInstance($this->container)->delete($this->uid, $id);
        return new ServiceResponse($data);
    }

    /**
     * 更改车位状态
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
            'status' => v::in([1,2])->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $status = intval($params['status']);
        $data = CarPlaceModule::getInstance($this->container)->changeStatus($this->uid, $id, $status);
        return new ServiceResponse($data);
    }
}