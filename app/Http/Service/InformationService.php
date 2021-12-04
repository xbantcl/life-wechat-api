<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\InformationModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Rules\In;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class InformationService extends Service
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
     * 添加动态消息
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function add(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'subdistrict_id' => v::optional(v::numericVal()),
            'title' => v::notEmpty(),
            'content' => v::notEmpty(),
            'images'  => v::notEmpty(),
            'category' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $title = trim($params['title']);
        $content = trim($params['content']);
        $images = trim($params['images']);
        $subdistrictId = isset($params['subdistrict_id']) ? intval($params['subdistrict_id']) : 0;
        $subdistrict = isset($params['subdistrict']) ? trim($params['subdistrict']) : '';
        $address = isset($params['address']) ? trim($params['address']) : '';
        $gpsAddress = isset($params['gps_address']) ? trim($params['gps_address']) : '';
        $lat = isset($params['lat']) ? trim($params['lat']) : 0;
        $lng = isset($params['lng']) ? trim($params['lng']) : 0;
        $category = trim($params['category']);
        $data = InformationModule::getInstance($this->container)->add($this->uid, $title, $content, $images, $subdistrictId,
            $subdistrict, $address, $gpsAddress, $lat, $lng, $category);
        return new ServiceResponse($data);
    }

    /**
     * 获取动态信息数据列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function getList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'start' => v::optional(v::intVal()),
            'limit'  => v::optional(v::intVal()),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 10;
        $isPullDown = boolval($params['is_pull_down']);
        $isSelf = isset($params['is_self']) ? trim($params['is_self']) : false;
        $data = InformationModule::getInstance($this->container)->getList($this->uid, $isSelf, $start, $isPullDown, $limit);
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
        $data = InformationModule::getInstance($this->container)->getListByUid($this->uid, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 删除动态信息
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = InformationModule::getInstance($this->container)->delete($this->uid, $id);
        return new ServiceResponse($data);
    }

    /**
     * 更改动态状态
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
        $data = InformationModule::getInstance($this->container)->changeStatus($id, $status);
        return new ServiceResponse($data);
    }
}