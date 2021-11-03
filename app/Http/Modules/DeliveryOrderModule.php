<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Exception\DeliveryOrderException;
use Dolphin\Ting\Http\Model\DeliveryOrder;

class DeliveryOrderModule extends Module
{
    /**
     * 添加取快递订单
     *
     * @param $uid
     * @param $addressId
     * @param $price
     * @param $packageNum
     * @param $packageQua
     * @param $weight
     * @param $remarks
     * @return mixed
     * @throws DeliveryOrderException
     */
    public function add($uid, $addressId, $price, $packageNum, $packageQua, $weight, $remarks)
    {
        try {
            $order = DeliveryOrder::create([
                'uid' => $uid,
                'address_id' => $addressId,
                'price' => $price,
                'package_qua' => $packageQua,
                'package_num' => $packageNum,
                'weight' => $weight,
                'remarks' => $remarks,
                'status' => CommonConstant::ON_SHELVES
            ]);
        } catch (\Exception $e) {
            throw new DeliveryOrderException('ADD_DELIVERY_ORDER_ERROR');
        }
        return $order->id;
    }

    /**
     * 获取快递列表
     *
     * @param $uid
     * @param $start
     * @param $status
     * @param bool $isPullDown
     * @param int $limit
     * @return array
     * @throws DeliveryOrderException
     */
    public function getList($uid, $start, $status, $isPullDown = false, $limit = 5)
    {
        try {
            if ($status == 2) {
                $status = [1, 2];
            } else {
                $status = [$status];
            }
            $query = DeliveryOrder::whereIn('status', $status)
                ->select('id', 'package_qua', 'weight', 'price', 'status', 'updated_at')
                ->orderBy('id', 'desc');
            if ($uid != 1) {
                $query = $query->where('uid', $uid);
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
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new DeliveryOrderException('GET_DELIVERY_ORDER_LIST_ERROR');
        }
    }

    /**
     * 获取订单详情
     *
     * @param $id
     * @return mixed
     * @throws DeliveryOrderException
     */
    public function detail($id)
    {
        try {
            $data = DeliveryOrder::leftjoin('address as adr', 'adr.id', '=', 'delivery_orders.address_id')
                ->select('delivery_orders.id', 'delivery_orders.package_qua', 'delivery_orders.weight', 'delivery_orders.price',
                    'delivery_orders.status', 'delivery_orders.updated_at', 'adr.name', 'adr.address', 'adr.mobile')
                ->where('delivery_orders.id', $id)
                ->first()->toArray();
            if (!empty($data)) {
                $data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));
            }
            return $data;
        } catch (\Exception $e) {
            throw new DeliveryOrderException('GET_DELIVERY_ORDER_DETAIL_ERROR');
        }
    }

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @param $status
     * @return mixed
     * @throws DeliveryOrderException
     */
    public function changeStatus($uid, $id, $status)
    {
        try {
            if ($uid == 1) {
                DeliveryOrder::where('id', $id)->update([
                    'status' => $status
                ]);
            }
            return true;
        } catch (\Exception $e) {
            throw new DeliveryOrderException('CHANGE_DATA_STATUS_ERROR');
        }
    }

    /**
     * 删除数据状态
     * @param $uid
     * @param $id
     * @return mixed
     * @throws DeliveryOrderException
     */
    public function delete($uid, $id)
    {
        try {
            if ($uid != 1) {
                DeliveryOrder::where('id', $id)->where('uid', $uid)->delete();
            } else {
                DeliveryOrder::where('id', $id)->delete();
            }
            return true;
        } catch (\Exception $e) {
            throw new DeliveryOrderException('DELETE_DATA_ERROR');
        }
    }
}