<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Exception\RentException;
use Dolphin\Ting\Http\Exception\RiskyException;
use Dolphin\Ting\Http\Model\Rent;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Utils\Geohash;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;

class RentModule extends Module
{
    private $openid;
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->openid = $container->get('Config')['weixin']['program']['openid'];
    }
    /**
     * 发布租用信息
     *
     * @param $uid
     * @param $type
     * @param $price
     * @param $mobile
     * @param $title
     * @param $category
     * @param $address
     * @param $lat
     * @param $lng
     * @param $desc
     * @param $images
     *
     * @return mixed
     *
     * @throws RentException
     */
    public function add($uid, $type, $price, $mobile, $title, $category, $address, $lat, $lng, $desc, $images)
    {
        try {

            $accessToken = CacheModule::getInstance($this->container)->getAccessToken();
            $res = Help::secCheckContent($accessToken, $this->openid, 2, $title.$desc);
            if ($res !== 'pass') {
                throw new RiskyException('COMMENT_NOT_PASS');
            }

            $rent = Rent::create([
                'uid' => $uid,
                'type' => $type,
                'status' => 2,
                'price' => $price,
                'mobile' => $mobile,
                'title' => $title,
                'category' => $category,
                'address' => $address,
                'lat' => $lat,
                'lng' => $lng,
                'desc' => $desc,
                'images' => $images
            ]);
        } catch (RiskyException $e) {
            throw new RiskyException('COMMENT_NOT_PASS');
        } catch (\Exception $e) {
            throw new RentException('ADD_RENT_DATA_ERROR');
        }
        return $rent->id;
    }

    /**
     * 获取租借列表
     *
     * @param $start
     * @param $type
     * @param title
     * @param bool $isPullDown
     * @param int $limit
     * @return array
     *
     * @throws RentException
     */
    public function getList($start, $type, $title = '', $isPullDown = false, $limit = 5, $lat = '', $lng = '')
    {
        try {
            $query = Rent::where('status', 2)
                ->select('id', 'type', 'title', 'address', 'price', 'images', 'desc', 'lat', 'lng', 'updated_at')
                ->orderBy('id', 'desc');
            if (strtolower($type) != 'all') {
                $query = $query->where('type', $type);
            }
            if (!empty($title)) {
                $query = $query->where('title', 'like', '%' . $title . '%');
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
            $geohash = new Geohash();
            $data = array_map(function ($item) use ($data, $lat, $lng, $geohash) {
                if (!empty($item['images'])) {
                    $item['thumb'] = ImageConstant::BASE_IMAGE_URL . current(explode('|', $item['images']));
                }
                $item['updated_at'] = Help::timeAgo(strtotime($item['updated_at']));
                if ($lat) {
                    $item['distance'] = $geohash->getDistance($lat, $lng, $item['lat'], $item['lng']);
                    unset($item['lat'], $item['lng']);
                }
                unset($item['images']);
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new RentException('GET_CAR_PLACE_LIST_ERROR');
        }
    }

    public function getListByUid($uid, $start, $limit, $isAdmin = false)
    {
        if ($uid === 1) {
            $isAdmin = true;
        }
        $query = CarPlace::select('id', 'content', 'images', 'created_at')
            ->orderBy('id', 'DESC');
        if (!$isAdmin) {
            $query->where('uid', '=', $uid);
        }
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
        foreach ($data as $index => &$item) {
            $item['images'] = explode('|', $item['images']);
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
        }
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * 获取租借详情
     *
     * @param $id
     * @return mixed
     * @throws RentException
     */
    public function detail($id)
    {
        try {
            $data = Rent::select('id', 'type', 'title', 'mobile', 'images', 'price', 'desc', 'address', 'created_at')
                ->where('id', $id)
                ->where('status', CommonConstant::ON_SHELVES)
                ->first();
            if ($data instanceof Rent) {
                $data->images = array_map(function ($image) {
                    return ImageConstant::BASE_IMAGE_URL . $image;
                }, explode('|', $data->images));
                $data->created_time = date('Y-m-d', strtotime($data->created_at));
            } else {
                $data = [];
            }
            return $data;
        } catch (\Exception $e) {
            throw new RentException('GET_RENT_DETAIL_ERROR');
        }
    }

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @param $status
     * @return mixed
     * @throws RentException
     */
    public function changeStatus($uid, $id, $status)
    {
        try {
            if ($uid != 1) {
                Rent::where('id', $id)->where('uid', $uid)->update([
                    'post_status' => $status
                ]);
            } else {
                if ($status == 1) {
                    $status = CommonConstant::ADMIN_OFF_SHELVES;
                }
                Rent::where('id', $id)->update([
                    'post_status' => $status
                ]);
            }
            return true;
        } catch (\Exception $e) {
            throw new RentException('CHANGE_DATA_STATUS_ERROR');
        }
    }

    /**
     * 删除数据
     *
     * @param $uid
     * @param $id
     * @return array|Rent
     * @throws RentException
     */
    public function delete($uid, $id)
    {
        try {
            if ($uid !== 1) {
                Rent::where('id', $id)->where('uid', $uid)->delete();
            } else {
                Rent::where('id', $id)->delete();
            }
            return true;
        } catch (\Exception $e) {
            throw new RentException('DELETE_RENT_DATA_ERROR');
        }
    }
}