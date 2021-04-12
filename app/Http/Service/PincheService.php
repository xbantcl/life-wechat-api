<?php

namespace Dolphin\Ting\Http\Service;
use Dolphin\Ting\Http\Modules\PincheModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class PincheService extends Service
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
     * 发布拼车信息
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function add(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'type' => v::in([1, 2])->notEmpty(),
            'departure_name' => v::notEmpty(),
            'destination_name' => v::notEmpty(),
            'departure_address' => v::notEmpty(),
            'destination_address' => v::notEmpty(),
            'departure_lat' => v::notEmpty(),
            'departure_lng' => v::notEmpty(),
            'destination_lat' => v::notEmpty(),
            'destination_lng' => v::notEmpty(),
            'seat_num' => v::notEmpty(),
            'username' => v::notEmpty(),
            'mobile' => v::notEmpty(),
            'condition' => v::optional(v::notEmpty()),
            'images'  => v::notEmpty(),
            'start_time' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $departureName = isset($params['departure_name']) ? $params['departure_name'] : '';
        $destinationName = isset($params['destination_name']) ? $params['destination_name'] : '';
        $departureAddress = isset($params['departure_address']) ? $params['departure_address'] : '';
        $destinationAddress = isset($params['destination_address']) ? $params['destination_address'] : '';
        $departureLat = isset($params['departure_lat']) ? $params['departure_lat'] : '';
        $departureLng = isset($params['departure_lng']) ? $params['departure_lng'] : '';
        $destinationLat = isset($params['destination_lat']) ? $params['destination_lat'] : '';
        $destinationLng = isset($params['destination_lng']) ? $params['destination_lng'] : '';
        $images = isset($params['images']) ? $params['images'] : '';
        $username = isset($params['username']) ? $params['username'] : '';
        $seatNum = isset($params['seat_num']) ? $params['seat_num'] : '';
        $sex = isset($params['sex']) ? $params['sex'] : '男';
        $mobile = isset($params['mobile']) ? $params['mobile'] : '';
        $condition = isset($params['condition']) ? $params['condition'] : '';
        $startTime = isset($params['start_time']) ? $params['start_time'] : '';
        $type = isset($params['type']) ? $params['type'] : 1;
        $price = isset($params['price']) ? $params['price'] : 0;
        $data = PincheModule::getInstance($this->container)->add($this->uid, $type, $departureAddress, $destinationAddress, $departureLat, $departureLng, $destinationLat, $destinationLng,
            $condition, $price, $username, $mobile, $sex, $images, $seatNum, $startTime, $departureName, $destinationName);
        return new ServiceResponse($data);
    }

    /**
     * 获取拼车列表
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function getList (Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'type' => v::in(['all', 1, 2])->notEmpty(),
            'departure_lat' => v::notEmpty(),
            'departure_lng' => v::notEmpty(),
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $dptAddress = isset($params['dpt_address']) ? $params['dpt_address'] : '';
        $dstAddress = isset($params['dst_address']) ? $params['dst_address'] : '';
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $type = isset($params['type']) ? $params['type'] : 'all';
        $departureLat = isset($params['departure_lat']) ? $params['departure_lat'] : '';
        $departureLng = isset($params['departure_lng']) ? $params['departure_lng'] : '';
        $destinationLat = isset($params['destination_lat']) ? $params['destination_lat'] : '';
        $destinationLng = isset($params['destination_lng']) ? $params['destination_lng'] : '';
        $data = PincheModule::getInstance($this->container)->getList($type, $departureLat, $departureLng, $destinationLat, $destinationLng, $dptAddress, $dstAddress);
        return new ServiceResponse($data);
    }
}