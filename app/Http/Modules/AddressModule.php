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
     * @param $isDefault
     *
     * @return mixed
     * @throws AddressException
     */
    public function add($uid, $name, $mobile, $gpsAddress, $address, $lat, $lng, $isDefault)
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
            $data = Address::select('id', 'name', 'mobile', 'gps_address', 'lat', 'lng', 'address', 'mark', 'is_default')
                ->where('uid', $uid)
                ->orderBy('id', 'desc')
                ->get()->toArray();
            return ['list' => $data];
        } catch (\Exception $e) {
            throw new AddressException('GET_ADDRESS_LIST_ERROR');
        }
    }

    /**
     * 更新地址信息
     *
     * @param $id
     * @param $name
     * @param $mobile
     * @param $gpsAddress
     * @param $address
     * @param $isDefault
     *
     * @return mixed
     * @throws AddressException
     */
    public function update($uid, $id, $name, $mobile, $gpsAddress, $lat, $lng, $address, $isDefault)
    {
        try {
            if ($isDefault === 2) {
                Address::where('uid', $uid)->update(['is_default' => 1]);
            }
            $data = Address::where('id', $id)->update([
                'name' => $name,
                'mobile' => $mobile,
                'gps_address' => $gpsAddress,
                'lat' => $lat,
                'lng' => $lng,
                'address' => $address,
                'is_default' => $isDefault
            ]);

            return $data;
        } catch (\Exception $e) {
            throw new AddressException('UPDATE_ADDRESS_ERROR');
        }
    }

    /**
     * 获取地址信息
     *
     * @param $id
     * @return mixed
     *
     * @throws AddressException
     */
    public function detail($id)
    {
        try {
            $data = Address::select('id', 'name', 'mobile', 'gps_address', 'lng', 'lat', 'address', 'mark', 'is_default')
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

    /**
     * 获取默认地址
     *
     * @param $uid
     * @return mixed
     * @throws AddressException
     */
    public function getDefaultAddress($uid)
    {
        try {
            return Address::where('uid', $uid)
                ->select('id', 'name', 'mobile', 'gps_address', 'address', 'lat', 'lng')
                ->where('is_default', 2)
                ->first();
        } catch (\Exception $e) {
            throw new AddressException('GET_ADDRESS_ERROR');
        }
    }
}