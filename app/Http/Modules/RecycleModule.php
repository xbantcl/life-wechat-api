<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Constant\RecycleConstant;
use Dolphin\Ting\Http\Exception\RecycleException;
use Dolphin\Ting\Http\Exception\RentException;
use Dolphin\Ting\Http\Model\Recycle;
use Dolphin\Ting\Http\Model\Rent;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Utils\Geohash;
use Dolphin\Ting\Http\Utils\Help;

class RecycleModule extends Module
{
    /**
     * 创建回收订单
     *
     * @param $uid
     * @param $category
     * @param $addressId
     * @param $weight
     * @param $appointmentTime
     * @param $mark
     *
     * @return mixed
     * @throws RentException
     */
    public function add($uid, $category, $addressId, $weight, $appointmentTime, $mark)
    {
        try {
            $recycle = Recycle::create([
                'uid' => $uid,
                'category' => $category,
                'status' => RecycleConstant::APPOINTMENT,
                'address_id' => $addressId,
                'weight' => $weight,
                'appointment_time' => $appointmentTime,
                'mark' => $mark
            ]);
        } catch (\Exception $e) {
            throw new RecycleException('ADD_RECYCLE_DATA_ERROR');
        }
        return $recycle->id;
    }

    /**
     *
     *  更新回收订单
     *
     * @param $orderId
     * @param $uid
     * @param $category
     * @param $addressId
     * @param $weight
     * @param $appointmentTime
     * @param $mark
     *
     * @return mixed
     * @throws RentException
     */
    public function update($orderId, $uid, $category, $addressId, $weight, $appointmentTime, $mark, $actualWeight)
    {
        try {
            Recycle::where('id', $orderId)
                ->where('uid', $uid)
                ->update([
                    'category' => $category,
                    'status' => RecycleConstant::APPOINTMENT,
                    'address_id' => $addressId,
                    'weight' => $weight,
                    'appointment_time' => $appointmentTime,
                    'actual_weight' => $actualWeight,
                    'mark' => $mark
                ]);
        } catch (\Exception $e) {
            throw new RecycleException('UPDATE_RECYCLE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 更新订单状态
     *
     * @param $orderId
     * @param $uid
     * @param $status
     * @param $actualWeight
     * @return bool
     * @throws RecycleException
     */
    public function updateStatus($orderId, $uid, $status, $actualWeight = 0)
    {
        try {
            $data = ['status' => $status];
            if ($actualWeight) {
                $data['actual_weight'] = $actualWeight;
            }
            Recycle::where('id', $orderId)
                ->where('uid', $uid)
                ->update($data);
        } catch (\Exception $e) {
            throw new RecycleException('UPDATE_RECYCLE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 获取回收订单列表
     *
     * @param $uid
     * @param $start
     * @param $status
     * @param bool $isPullDown
     * @param int $limit
     *
     * @return array
     * @throws RecycleException
     */
    public function getList($uid, $status, $isPullDown = false, $start = 0, $limit = 5)
    {
        try {
            $query = Recycle::leftjoin('address as adr', 'adr.id', '=', 'recycle_order.address_id')
                ->select('recycle_order.id', 'recycle_order.category', 'recycle_order.weight', 'recycle_order.actual_weight',
                    'recycle_order.status', 'adr.address')
                ->orderBy('recycle_order.id', 'desc');
            if ($uid !== 1) {
                $query->where('recycle_order.uid', $uid);
            }
            $query->where('recycle_order.status', $status);
            if ($start > 0) {
                if ($isPullDown) {
                    $query->where('recycle_order.id', '>', $start);
                } else {
                    $query->where('recycle_order.id', '<', $start);
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
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new RecycleException('GET_RECYCLE_ORDER_LIST_ERROR');
        }
    }

    /**
     * 获取订单详情
     *
     * @param $id
     *
     * @return mixed
     * @throws RecycleException
     */
    public function detail($id)
    {
        try {
            $data = Recycle::leftjoin('address as adr', 'adr.id', '=', 'recycle_order.address_id')
                ->select('recycle_order.id', 'recycle_order.category', 'recycle_order.weight', 'recycle_order.actual_weight',
                    'recycle_order.status', 'adr.address', 'adr.name', 'adr.mobile', 'adr.gps_address', 'adr.lat', 'adr.lng')
                ->where('recycle_order.id', $id)
                ->first()->toArray();
            return $data;
        } catch (\Exception $e) {
            throw new RecycleException('GET_RECYCLE_ORDER_DETAIL_ERROR');
        }
    }

    /**
     * 删除订单
     *
     * @param $id
     * @return bool
     *
     * @throws RecycleException
     */
    public function delete($id)
    {
        try {
            Recycle::where('id', $id)->delete();
            return true;
        } catch (\Exception $e) {
            throw new RecycleException('DELETE_RECYCLE_ORDER_ERROR');
        }
    }

}