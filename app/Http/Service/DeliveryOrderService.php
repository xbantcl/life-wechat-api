<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\DeliveryOrderModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 快递相关接口类
class DeliveryOrderService extends Service
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
     * 发布快递信息
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'address_id' => v::numericVal()->notEmpty(),
            'price' => v::floatVal(),
            'package_num' => v::notEmpty(),
            'package_qua' => v::numericVal()->notEmpty(),
            'weight' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $addressId = intval($params['address_id']);
        $price = floatval($params['price']);
        $packageNum = trim($params['package_num']);
        $packageQua = intval($params['package_qua']);
        $weight = trim($params['weight']);
        $remarks = isset($params['remarks']) ? trim($params['remarks']) : '';
        $data = DeliveryOrderModule::getInstance($this->container)->add($this->uid, $addressId, $price, $packageNum, $packageQua, $weight, $remarks);
        return new ServiceResponse($data);
    }

    /**
     * 获取快递列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'status' => v::in([1, 2, 3])->notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 10;
        $status = intval($params['status']);
        $isPullDown = isset($params['is_pull_down']) ? boolval($params['is_pull_down']) : false;
        $data = DeliveryOrderModule::getInstance($this->container)->getList($this->uid, $start, $status, $isPullDown, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取快递订单详情
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
        $id = intval($params['id']);
        $data = DeliveryOrderModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }

    /**
     * 更改快递订单状态
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
            'status' => v::in([1, 3])->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $status = intval($params['status']);
        $data = DeliveryOrderModule::getInstance($this->container)->changeStatus($this->uid, $id, $status);
        return new ServiceResponse($data);
    }
}