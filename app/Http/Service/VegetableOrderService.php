<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\VegetableOrderModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 买菜相关接口类
class VegetableOrderService extends Service
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
     * 添加买菜订单
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'order_no' => v::notEmpty(),
            'address_id' => v::notEmpty(),
            'product_num' => v::numericVal()->notEmpty(),
            'products' => v::notEmpty(),
            'amount' => v::floatVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $orderNo = trim($params['order_no']);
        $addressId = trim($params['address_id']);
        $productNum = intval($params['product_num']);
        $products = trim($params['products']);
        $amount = floatval($params['amount']);
        $remarks = isset($params['remarks']) ? trim($params['remarks']) : '';
        $data = VegetableOrderModule::getInstance($this->container)->add($this->uid, $orderNo, $addressId, $productNum, $products, $amount, $remarks);
        return new ServiceResponse($data);
    }

    /**
     * 更新买菜订单信息
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function update(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'order_no' => v::notEmpty(),
            'status' => v::in([1, 2, 3]),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $orderNo = trim($params['order_no']);
        $status = intval($params['status']);
        $data = VegetableOrderModule::getInstance($this->container)->update($this->uid, $orderNo, $status);
        return new ServiceResponse($data);
    }

    /**
     * 获取买菜订单列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'status' => v::in([0, 1, 2, 3]),
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
        $data = VegetableOrderModule::getInstance($this->container)->getList($this->uid, $status, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取买菜订单详情
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function detail (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'order_no' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $orderNo = intval($params['order_no']);
        $data = VegetableOrderModule::getInstance($this->container)->detail($orderNo);
        return new ServiceResponse($data);
    }

    /**
     * 删除买菜订单
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'order_no'  => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $orderNo = intval($params['orderNo']);
        $data = VegetableOrderModule::getInstance($this->container)->delete($orderNo);
        return new ServiceResponse($data);
    }
}