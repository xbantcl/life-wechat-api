<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\AddressException;
use Dolphin\Ting\Http\Model\Address;
use Dolphin\Ting\Http\Utils\Geohash;
use Dolphin\Ting\Http\Utils\Help;

class AddressModule extends Module
{
    /**
     *  添加地址信息
     *
     * @param $uid
     * @param $name
     * @param $mobile
     * @param $gpsAddress
     * @param $address
     * @param $lat
     * @param $lng
     * @param $mark
     * @param $isDefault
     *
     * @return mixed
     * @throws AddressException
     */
    public function add($uid, $name, $mobile, $gpsAddress, $address, $lat, $lng, $mark, $isDefault)
    {
        try {
            $addressObj = Address::create([
                'uid' => $uid,
                'name' => $name,
                'mobile' => $mobile,
                'gps_address' => $gpsAddress,
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                'mark' => $mark,
                'is_default' => $isDefault
            ]);
        } catch (\Exception $e) {
            throw new AddressException('ADD_ADDRESS_DATA_ERROR');
        }
        return $addressObj->id;
    }

    /**
     * 获取地址列表
     *
     * @param int $uid
     *
     * @return array
     *
     * @throws AddressException
     */
    public function getList($uid)
    {
        try {
            $data = Address::select('id', 'name', 'mobile', 'gps_address', 'address', 'mark', 'is_default')
                ->where('uid', $uid)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            return ['list' => $data];
        } catch (\Exception $e) {
            throw new AddressException('GET_ADDRESS_LIST_ERROR');
        }
    }

    /**
     * 获取地址详情
     *
     * @param $id
     * @return mixed
     *
     * @throws AddressException
     */
    public function detail($id)
    {
        try {
            $data = Address::select('id', 'name', 'mobile', 'gps_address', 'address', 'mark', 'is_default')
                ->where('id', $id)
                ->first();
            return $data;
        } catch (\Exception $e) {
            throw new AddressException('GET_ADDRESS_DETAIL_ERROR');
        }
    }

    /**
     * 删除地址
     *
     * @param $uid
     * @param $id
     * @return bool
     *
     * @throws AddressException
     */
    public function delete($uid, $id)
    {
        try {
            Address::where('id', $id)->where('uid', $uid)->delete();
            return true;
        } catch (\Exception $e) {
            throw new AddressException('DELETE_ADDRESS_ERROR');
        }
    }

}