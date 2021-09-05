<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\AddressModule;
use Dolphin\Ting\Http\Modules\RentModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

// 回收相关接口类
class AddressService extends Service
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
     * 添加地址
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
     */
    public function add (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'name' => v::notEmpty(),
            'mobile' => v::numericVal(),
            'address' => v::notEmpty(),
            'gps_address' => v::notEmpty(),
            'lat' => v::notEmpty(),
            'lng' => v::notEmpty(),
            'is_default' => v::in([1,2])->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $name = isset($params['name']) ? trim($params['name']) : '';
        $mobile = isset($params['mobile']) ? trim($params['mobile']) : '';
        $gpsAddress = isset($params['gps_address']) ? trim($params['gps_address']) : '';
        $address = isset($params['address']) ? trim($params['address']) : '';
        $lat = isset($params['lat']) ? trim($params['lat']) : '';
        $lng = isset($params['lng']) ? trim($params['lng']) : '';
        $mark = isset($params['mark']) ? trim($params['mark']) : '';
        $isDefault = intval($params['is_default']);
        $data = AddressModule::getInstance($this->container)->add($this->uid, $name, $mobile, $gpsAddress, $address, $lat, $lng, $mark, $isDefault);
        return new ServiceResponse($data);
    }

    /**
     * 获取地址列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $data = AddressModule::getInstance($this->container)->getList();
        return new ServiceResponse($data);
    }

    /**
     * 获取地址详情
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
        $data = AddressModule::getInstance($this->container)->detail($id);
        return new ServiceResponse($data);
    }

    /**
     * 删除地址评论
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'id'  => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $id = intval($params['id']);
        $data = AddressModule::getInstance($this->container)->delete($this->uid, $id);
        return new ServiceResponse($data);
    }
}