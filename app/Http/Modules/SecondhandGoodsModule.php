<?php

namespace Dolphin\Ting\Http\Modules;


use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Constant\SecondhandGoodsConstant;

use Dolphin\Ting\Http\Exception\RiskyException;
use Dolphin\Ting\Http\Exception\SecondhandGoodsException;
use Dolphin\Ting\Http\Model\SecondhandGoods;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;

class SecondhandGoodsModule extends Module
{
    private $openid;
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->openid = $container->get('Config')['weixin']['program']['openid'];
    }

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
    public function add($uid, $title, $price, $originalPrice, $address, $describe, $delivery, $images, $category, $mobile)
    {
        try {
            $status = CommonConstant::AUDIT;
            $accessToken = CacheModule::getInstance($this->container)->getAccessToken();
            $res = Help::secCheckContent($accessToken, $this->openid, 2, $title.$describe);
            if ($res == 'review') {
                $status = CommonConstant::ADMIN_OFF_SHELVES;
            } elseif ($res == 'risky') {
                throw new RiskyException('CONTENT_NOT_PASS');
            }
            $secondhandGood = SecondhandGoods::create([
                'uid' => $uid,
                'status' => $status, // 目前是自动上架
                'price' => $price,
                'title' => $title,
                'original_price' => $originalPrice,
                'address' => $address,
                'describe' => $describe,
                'delivery' => $delivery,
                'category' => $category,
                'images' => $images,
                'mobile' => $mobile,
                'post_status' => $status
            ]);
        } catch (RiskyException $e) {
            throw new RiskyException('CONTENT_NOT_PASS');
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
            $query = SecondhandGoods::leftjoin('user as u', 'u.id', '=', 'secondhand_goods.uid')
                ->select('secondhand_goods.id', 'secondhand_goods.title', 'secondhand_goods.original_price', 'secondhand_goods.price',
                    'secondhand_goods.images', 'secondhand_goods.updated_at', 'u.username', 'u.avatar')
                ->where('secondhand_goods.post_status', CommonConstant::ON_SHELVES)
                ->orderBy('secondhand_goods.id', 'desc');
            if (strtolower($category) !== 'all') {
                $query = $query->where('secondhand_goods.category', '=', $category);
            }
            if ($start > 0) {
                if ($isPullDown) {
                    $query->where('secondhand_goods.id', '>', $start);
                } else {
                    $query->where('secondhand_goods.id', '<', $start);
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
                $item['updated_at'] = Help::formatTime($item['updated_at']);
                unset($item['images']);
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('GET_SECONDHAND_GOODS_LIST_ERROR');
        }
    }

    /**
     * 获取用户闲置商品
     *
     * @param $uid
     * @param $start
     * @param $limit
     * @param $isAdmin
     * @return array
     */
    public function getListByUid($uid, $start, $limit, $isAdmin = false) {
        if ($uid === 1) {
            $isAdmin = true;
        }
        $query = SecondhandGoods::leftjoin('user as u', 'u.id', '=', 'secondhand_goods.uid')
            ->select('secondhand_goods.id', 'secondhand_goods.post_status', 'secondhand_goods.title', 'secondhand_goods.original_price', 'secondhand_goods.price',
                'secondhand_goods.images', 'secondhand_goods.updated_at', 'u.username', 'u.avatar')
            ->orderBy('secondhand_goods.id', 'desc');
        if ($uid !== 1) {
            $query->where('secondhand_goods.uid', $uid);
        }
        if ($start > 0) {
            $query->where('secondhand_goods.id', '<', $start);
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
        foreach ($data as $index => &$item) {
            $item['images'] = array_map(function ($image) {
                return ImageConstant::BASE_IMAGE_URL . $image;
            }, explode('|', $item['images']));
            $item['thumb'] = $item['images'][0];
            $item['updated_at'] = date('Y-m-d', strtotime($item['updated_at']));
        }
        return ['start' => $start, 'more' => $more, 'list' => $data];
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
                'u.username', 'u.avatar', 'secondhand_goods.mobile', 'secondhand_goods.delivery', 'secondhand_goods.uid')
                ->where('secondhand_goods.id', $id)
                ->where('post_status', CommonConstant::ON_SHELVES)
                ->first();
            if (!empty($data)) {
                $data = $data->toArray();
                $data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));
                $data['images'] = explode('|', $data['images']);
            } else {
                $data = [];
            }
            return $data;
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('GET_SECONDHAND_GOODS_DETAIL_ERROR');
        }
    }

    /**
     * 删除闲置商品
     *
     * @param $uid
     * @param $id
     * @return bool
     * @throws SecondhandGoodsException
     */
    public function delete($uid, $id)
    {
        try {
            if ($uid == 1) {
                SecondhandGoods::where('id', $id)->delete();
            } else {
                SecondhandGoods::where('id', $id)->where('uid', $uid)->delete();
            }
            return true;
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('DELETE_GOODS_ERROR');
        }
    }

    /**
     * 更改闲置商品状态
     *
     * @param $uid
     * @param $id
     * @return bool
     * @throws SecondhandGoodsException
     */
    public function changeStatus($uid, $id, $status)
    {
        try {
            if ($uid !== 1) {
                $data = SecondhandGoods::select('id', 'post_status')->where('id', $id)->first();
                if ($data instanceof SecondhandGoods && $data->post_status !== CommonConstant::ADMIN_OFF_SHELVES) {
                    SecondhandGoods::where('id', $id)->where('uid', $uid)->update(['post_status' => $status]);
                }
            } else {
                if ($status == 1) {
                    $status = CommonConstant::ADMIN_OFF_SHELVES;
                }
                SecondhandGoods::where('id', $id)->update(['post_status' => $status]);
            }
            return true;
        } catch (\Exception $e) {
            throw new SecondhandGoodsException('UPDATE_GOODS_ERROR');
        }
    }
}