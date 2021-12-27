<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Exception\VegetableOrderException;
use Dolphin\Ting\Http\Model\VegetableOrders;

class VegetableOrderModule extends Module
{
    /**
     * 添加菜订单
     *
     * @param $name
     * @param $price
     * @param $desc
     * @param $images
     * @return mixed
     *
     * @throws VegetableOrderException
     */
    public function add($uid, $orderNo, $addressId, $productNum, $products, $amount, $remarks)
    {
        try {
            $order = VegetableOrders::create([
                'uid' => $uid,
                'order_no' => $orderNo,
                'status' => CommonConstant::OFF_SHELVES,
                'address_id' => $addressId,
                'product_num' => $productNum,
                'products' => $products,
                'amount' => $amount,
                'weight' => 5,
                'appointment_time' => time(),
                'remarks' => $remarks
            ]);
        } catch (\Exception $e) {
            throw new VegetableOrderException('ADD_VEGETABLE_ORDER_DATA_ERROR');
        }
        return $order->id;
    }

    /**
     * 更新菜品
     *
     * @param $id
     * @param $name
     * @param $price
     * @param $desc
     * @param $images
     *
     * @return bool
     *
     * @throws VegetableOrderException
     */
    public function update($orderNo, $status)
    {
        try {
            VegetableOrders::where('order_no', $orderNo)->update([
                'status' => $status
            ]);
        } catch (\Exception $e) {
            throw new VegetableOrderException('UPDATE_VEGETABLE_ORDER_DATA_ERROR');
        }
        return true;
    }

    /**
     * 获取买菜订单列表
     *
     * @param $uid
     * @param $status
     * @param $start
     * @param $limit
     * @return array
     *
     * @throws VegetableOrderException
     */
    public function getList($uid, $status, $start = 0, $limit = 20)
    {
        try {
            $query = VegetableOrders::select('id', 'order_no', 'status', 'product_num', 'products', 'amount', 'created_at');
            if ($status != 0) {
                $query->where('status', $status);
            }
            if ($uid !== 1) {
                $query->where('uid', $uid);
            }
            if ($start > 0) {
                $query->where('id', '>', $start);
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
            foreach ($data as &$item) {
                $item['products'] = json_decode($item['products'], true);
                $item['created_time'] = date('Y-m-d H:i:s', strtotime($item['created_at']));
                $item['name'] = array_map(function ($d) {
                    return $d['name'];
                }, $item['products']);
                unset($item['products']);
            }
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new VegetableOrderException('GET_VEGETABLES_ORDER_LIST_ERROR');
        }
    }

    /**
     * 获取买菜订单详情
     *
     * @param $id
     * @return mixed
     *
     * @throws VegetableOrderException
     */
    public function detail($orderNo)
    {
        try {
            $data = VegetableOrders::select('id', 'order_no', 'products', 'created_at')
                ->where('order_no', $orderNo)
                ->first();
            if ($data instanceof VegetableOrders) {
                $data->products = json_decode($data->products, true);
                $data->created_time = date('Y-m-d H:i:s', $data->created_at);
            }
            return $data;
        } catch (\Exception $e) {
            throw new VegetableOrderException('GET_VEGETABLES_DETAIL_ERROR');
        }
    }

    /**
     * 删除买菜订单
     *
     * @param $uid
     * @param $orderNo
     * @return bool
     *
     * @throws VegetableOrderException
     */
    public function delete($uid, $orderNo)
    {
        try {
            VegetableOrders::where('order_no', $orderNo)
                ->where('uid', $uid)
                ->delete();
            return true;
        } catch (\Exception $e) {
            throw new VegetableOrderException('DELETE_VEGETABLES_ORDER_ERROR');
        }
    }
}