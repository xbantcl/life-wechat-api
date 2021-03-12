<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Exception\CarPlaceException;
use Dolphin\Ting\Http\Model\CarPlace;
use Dolphin\Ting\Http\Utils\Help;

class CarPlaceModule extends Module
{
    /**
     * 发布车位信息
     *
     * @param $carPlaceStatus
     * @param $price
     * @param $isStandard
     * @param $floorage
     * @param $floor
     * @param $subdistrict
     * @param $buildingNum
     * @param $describe
     * @param $phoneNum
     * @param $weixin
     * @param $images
     *
     * @return mixed
     * @throws CarPlaceException
     */
    public function add($carPlaceStatus, $price, $isStandard, $floorage, $floor, $subdistrict, $buildingNum, $describe, $phoneNum, $weixin, $images)
    {
        try {
            $carPlace = CarPlace::create([
                'uid' => 1,
                'subdistrict_id' => 1,
                'car_place_status' => $carPlaceStatus,
                'price' => $price,
                'is_standard' => $isStandard,
                'floorage' => $floorage,
                'floor' => $floor,
                'subdistrict' => $subdistrict,
                'building_number' => $buildingNum,
                'describe' => $describe,
                'phone_number' => $phoneNum,
                'weixin' => $weixin,
                'images' => $images
            ]);
        } catch (\Exception $e) {
            throw new CarPlaceException('ADD_CAR_PLACE_ERROR');
        }
        return $carPlace->id;
    }

    /**
     * 获取车位列表
     *
     * @param int $start
     * @param int $limit
     *
     * @return mixed
     */
    public function getList($start, $limit = 5)
    {
        try {
            $query = CarPlace::where('post_status', '=', CarPlaceConstant::ON_SHELVES)
                ->select('id', 'car_place_status', 'is_standard', 'floor', 'subdistrict', 'images', 'building_number', 'updated_at')
                ->orderBy('id', 'desc');
            if ($start > 0) {
                $query->where('id', '<', $start);
            }
            $data = $query->take($limit + 1)->get()->toArray();
            $more = 0;
            if (empty($data)) {
                return ['start' => $start, 'more' => $more, 'list' => (object)[]];
            }
            if (count($data) > $limit) {
                $more = 1;
                array_pop($data);
            }
            $start = end($data)['id'];
            $data = array_map(function ($item) use ($data) {
                $item['images'] = explode('|', $item['images']);
                $item['updated_at'] = Help::timeAgo(strtotime($item['updated_at']));
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new CarPlaceException('GET_CAR_PLACE_LIST_ERROR');
        }
    }
}