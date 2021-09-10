<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Model\Recycle;
use Dolphin\Ting\Http\Modules\RecycleModule;
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
            'address_id' => v::intVal()->notEmpty(),
            'appointment_time' => v::notEmpty(),
            'weight' => v::notEmpty(),
            'category' => v::in(['paper', 'plastic', 'metal', 'clothes', 'electronic'])->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $addressId = intval($params['address_id']);
        $appointmentTime = trim($params['appointmentTime']);
        $weight = trim($params['weight']);
        $category = trim($params['category']);
        $mark = isset($params['mark']) ? trim($params['mark']) : '';
        $data = RecycleModule::getInstance($this->container)->add($this->uid, $category, $addressId, $weight, $appointmentTime, $mark);
        return new ServiceResponse($data);
    }

    /**
     * 更新回收预约
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function update (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty(),
            'address_id' => v::intVal()->notEmpty(),
            'appointment_time' => v::notEmpty(),
            'weight' => v::notEmpty(),
            'category' => v::in(['paper', 'plastic', 'metal', 'clothes', 'electronic'])->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $addressId = intval($params['address_id']);
        $appointmentTime = trim($params['appointmentTime']);
        $weight = trim($params['weight']);
        $category = trim($params['category']);
        $mark = isset($params['mark']) ? trim($params['mark']) : '';
        $data = RecycleModule::getInstance($this->container)->update($id, $this->uid, $category, $addressId, $weight, $appointmentTime, $mark);
        return new ServiceResponse($data);
    }

    /**
     * 更新回收预约状态
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function updateStatus(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id' => v::intVal()->notEmpty(),
            'status' => v::in([1, 2, 3, 4])->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $actualWeight = isset($params['actual_weight']) ? floatval($params['actual_weight']) : 0;
        $status = intval($params['status']);
        $data = RecycleModule::getInstance($this->container)->updateStatus($id, $this->uid, $status, $actualWeight);
        return new ServiceResponse($data);
    }

    /**
     * 获取回收预约列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'status' => v::in([1, 2, 3, 4])->notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal()),
            'is_pull_down' => v::in([0, 1]),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $status = intval($params['status']);
        $isPullDown = boolval($params['is_pull_down']);
        $data = RecycleModule::getInstance($this->container)->getList($this->uid, $status, $isPullDown, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取回收订单详情
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
        $data = RecycleModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }

    /**
     * 删除订单
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
        $id = intval($params['id']);
        $data = RecycleModule::getInstance($this->container)->delete($id);
        return new ServiceResponse($data);
    }
}