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

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
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
            'status' => v::in(['出租', '出售'])->notEmpty(),
            'price' => v::floatVal(),
            'is_standard' => v::in([1, 2])->notEmpty(),
            'floorage' => v::floatVal(),
            'floor' => v::in(['负一楼', '负二楼', '地面'])->notEmpty(),
            'subdistrict' => v::notEmpty(),
            'building_num' => v::numericVal(),
            'phone_num' => v::numericVal(),
            'describe' => v::notEmpty(),
            'weixin' => v::notEmpty(),
            'images' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $status = isset($params['status']) ? trim($params['status']) : '出售';
        $price = isset($params['price']) ? floatval($params['price']) : 0;
        $isStandard = isset($params['is_standard']) ? intval($params['is_standard']) : 1;
        $floorage = isset($params['floorage']) ? floatval($params['floorage']) : 0;
        $floor = isset($params['floor']) ? trim($params['floor']) : '负一楼';
        $subdistrict = isset($params['subdistrict']) ? floatval($params['subdistrict']) : '南湖世纪';
        $buildingNum = isset($params['building_num']) ? trim($params['building_num']) : 1;
        $phoneNum = isset($params['phone_num']) ? trim($params['phone_num']) : '';
        $describe = isset($params['describe']) ? trim($params['describe']) : '';
        $weixin = isset($params['weixin']) ? trim($params['weixin']) : '';
        $images = isset($params['images']) ? trim($params['images']) : '';
        $data = CarPlaceModule::getInstance($this->container)->getList($status, $price, $isStandard, $floorage, $floor, $subdistrict, $buildingNum, $describe, $phoneNum, $weixin, $images);
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
            'start' => v::optional(v::numericVal()),
            'limit' => v::optional(v::numericVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 5;
        $data = CircleModule::getInstance($this->container)->getList($start, $limit);
        return new ServiceResponse($data);
    }
}