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
     * @param $type
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
    public function add($type, $price, $isStandard, $floorage, $floor, $subdistrict, $buildingNum, $describe, $phoneNum, $weixin, $images)
    {
        try {
            $carPlace = CarPlace::create([
                'uid' => 1,
                'subdistrict_id' => 1,
                'type' => $type,
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
     * @param $start
     * @param $type
     * @param bool $isPullDown
     * @param int $limit
     * @return array
     * @throws CarPlaceException
     */
    public function getList($start, $type, $isPullDown = false, $limit = 5)
    {
        try {
            $query = CarPlace::where('post_status', '=', CarPlaceConstant::ON_SHELVES)
                ->select('id', 'type', 'is_standard', 'floor', 'price', 'subdistrict', 'images', 'building_number', 'updated_at')
                ->orderBy('id', 'desc');
            if (strtolower($type) != 'all') {
                $query = $query->where('type', '=', $type);
            }
            if ($start > 0) {
                if ($isPullDown) {
                    $query->where('id', '>', $start);
                } else {
                    $query->where('id', '<', $start);
                }
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
            if ($isPullDown) {
                $start = current($data)['id'];
            } else {
                $start = end($data)['id'];
            }
            $data = array_map(function ($item) use ($data) {
                $item['thumb'] = current(explode('|', $item['images']));
                $item['updated_at'] = Help::timeAgo(strtotime($item['updated_at']));
                if ($item['is_standard'] === CarPlaceConstant::STANDARD) {
                    $item['is_standard'] = '标准车位';
                } else {
                    $item['is_standard'] = '非标准车位';
                }
                unset($item['images']);
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new CarPlaceException('GET_CAR_PLACE_LIST_ERROR');
        }
    }

    /**
     * 获取车位详情
     *
     * @param $id
     * @return mixed
     * @throws CarPlaceException
     */
    public function detail($id)
    {
        try {
            $data = CarPlace::select('type', 'is_standard', 'floor', 'uid', 'floorage', 'price',
                'subdistrict', 'images', 'building_number', 'updated_at', 'describe', 'weixin')
                ->where('post_status', '=', CarPlaceConstant::ON_SHELVES)
                ->first()->toArray();
            if (!empty($data)) {
                if ($data['is_standard'] == CarPlaceConstant::STANDARD) {
                    $data['is_standard'] = '标准车位';
                } else {
                    $data['is_standard'] = '非标准车位';
                }
                $data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));
                if ($data['type'] == '出售') {
                    $data['price'] = $data['price'] . '万';
                } else {
                    $data['price'] = $data['price'] . '元/月';
                }
            }
            return $data;
        } catch (\Exception $e) {
            throw new CarPlaceException('GET_CAR_PLACE_DETAIL_ERROR');
        }
    }
}