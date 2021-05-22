<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Exception\RentException;
use Dolphin\Ting\Http\Model\Rent;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Utils\Geohash;
use Dolphin\Ting\Http\Utils\Help;

class RentModule extends Module
{
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
     * @param bool $isPullDown
     * @param int $limit
     * @return array
     *
     * @throws RentException
     */
    public function getList($start, $type, $isPullDown = false, $limit = 5, $lat = '', $lng = '')
    {
        try {
            $query = Rent::where('status', 2)
                ->select('id', 'type', 'title', 'address', 'price', 'images', 'desc', 'lat', 'lng', 'updated_at')
                ->orderBy('id', 'desc');
            if (strtolower($type) != 'all') {
                $query = $query->where('type', $type);
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
            throw new CarPlaceException('GET_CAR_PLACE_LIST_ERROR');
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
     * 获取车位详情
     *
     * @param $id
     * @return mixed
     * @throws CarPlaceException
     */
    public function detail($id)
    {
        try {
            $data = CarPlace::select('id', 'type', 'is_standard', 'floor', 'uid', 'floorage', 'price',
                'subdistrict', 'images', 'building_number', 'updated_at', 'describe', 'weixin')
                ->where('post_status', '=', CarPlaceConstant::ON_SHELVES)
                ->where('id', '=', $id)
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
                $data['images'] = explode('|', $data['images']);
            }
            return $data;
        } catch (\Exception $e) {
            throw new CarPlaceException('GET_CAR_PLACE_DETAIL_ERROR');
        }
    }

    /**
     * 发布车位评论
     *
     * @param int    $uid        评论作者id
     * @param int    $replyUid   被回复的用户id
     * @param int    $carPlaceId 车位id
     * @param string $content    评论类容
     *
     * @return mixed
     *
     * @throws CarPlaceException
     */
    public function comment($uid, $replyUid, $carPlaceId, $content)
    {
        try {
            $carPlaceComment = CarPlaceComment::create([
                'uid' => $uid,
                'reply_uid' => $replyUid,
                'car_place_id' => $carPlaceId,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            throw new CarPlaceException('ADD_CAR_PLACE_COMMENT_ERROR');
        }
        return $carPlaceComment->id;
    }

    /**
     * 获取车位评论列表
     *
     * @param int $carPlaceId 车位id
     * @return mixed
     * @throws CarPlaceException
     */
    public function commentList($carPlaceId)
    {
        try {
            $comments = CarPlaceComment::leftjoin('user as u', 'u.id', '=', 'car_place_comments.uid')
                ->leftjoin('user as u1', 'u1.id', '=', 'car_place_comments.reply_uid')
                ->select('car_place_comments.id', 'car_place_comments.uid', 'u.username', 'u.avatar', 'u1.username as reply_username',
                    'u1.avatar as reply_avatar', 'car_place_comments.content', 'car_place_comments.car_place_id', 'car_place_comments.created_at')
                ->where('car_place_comments.car_place_id', $carPlaceId)
                ->orderBy('car_place_comments.created_at', 'desc')
                ->get()->toArray();
            if (!empty($comments)) {
                foreach ($comments as &$item) {
                    $item['created_at'] = strtotime($item['created_at']);
                }
            }
            return $comments;
        } catch (\Exception $e) {
            throw new CarPlaceException('GET_COMMENTS_ERROR');
        }
    }

    /**
     * 删除评论
     *
     * @param $uid
     * @param $id
     * @return bool
     * @throws CarPlaceException
     */
    public function deleteComment($uid, $id)
    {
        try {
            CarPlaceComment::where('id', $id)->where('uid', $uid)->delete();
            return true;
        } catch (\Exception $e) {
            throw new CarPlaceException('DELETE_COMMENT_ERROR');
        }
    }

}