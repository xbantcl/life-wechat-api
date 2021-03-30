<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Constant\SecondhandGoodsConstant;
use Dolphin\Ting\Http\Exception\CarPlaceException;
use Dolphin\Ting\Http\Exception\SecondhandGoodsException;
use Dolphin\Ting\Http\Model\SecondhandGoods;
use Dolphin\Ting\Http\Utils\Help;

class SecondhandGoodsModule extends Module
{
    /**
     * 发布商品信息
     *
     * @param $uid
     * @param $title
     * @param $price
     * @param $originalPrice
     * @param $address
     * @param $describe
     * @param $delivery
     * @param $images
     * @param $category
     *
     * @return mixed
     * @throws SecondhandGoodsException
     */
    public function add($uid, $title, $price, $originalPrice, $address, $describe, $delivery, $images, $category)
    {
        try {
            $secondhandGood = SecondhandGoods::create([
                'uid' => $uid,
                'status' => SecondhandGoodsConstant::ON_SHELVES, // 目前是自动上架
                'price' => $price,
                'title' => $title,
                'original_price' => $originalPrice,
                'address' => $address,
                'describe' => $describe,
                'delivery' => $delivery,
                'category' => $category,
                'images' => $images
            ]);
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('ADD_SECONDHAND_GOODS_ERROR');
        }
        return $secondhandGood->id;
    }

    /**
     * 获取商品列表
     *
     * @param $start
     * @param $category
     * @param bool $isPullDown
     * @param int $limit
     *
     * @return array
     * @throws SecondhandGoodsException
     */
    public function getList($start, $category, $isPullDown = false, $limit = 5)
    {
        try {
            $query = SecondhandGoods::where('status', '=', SecondhandGoodsConstant::ON_SHELVES)
                ->select('id', 'title', 'price', 'images', 'address', 'updated_at')
                ->orderBy('id', 'desc');
            if (strtolower($category) !== 'all') {
                $query = $query->where('category', '=', $category);
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
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('GET_SECONDHAND_GOODS_LIST_ERROR');
        }
    }

    /**
     * 获取商品详情
     *
     * @param $id
     * @return mixed
     * @throws SecondhandGoodsException
     */
    public function detail($id)
    {
        try {
            $data = SecondhandGoods::leftjoin('user as u', 'u.id', '=', 'secondhand_goods.uid')
                ->select('secondhand_goods.id', 'secondhand_goods.title', 'secondhand_goods.original_price', 'secondhand_goods.price',
                'secondhand_goods.address', 'secondhand_goods.images', 'secondhand_goods.updated_at', 'secondhand_goods.describe',
                'u.username', 'u.avatar')
                ->where('secondhand_goods.status', '=', SecondhandGoodsConstant::ON_SHELVES)
                ->where('secondhand_goods.id', '=', $id)
                ->first()->toArray();
            if (!empty($data)) {
                $data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));
                $data['images'] = explode('|', $data['images']);
            }
            return $data;
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('GET_SECONDHAND_GOODS_DETAIL_ERROR');
        }
    }
}