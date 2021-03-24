<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Exception\CarPlaceException;
use Dolphin\Ting\Http\Model\CarPlace;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Utils\Help;

class CarPlaceModule extends Module
{
    /**
     * 发布车位信息
     *
     * @param $uid
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
    public function add($uid, $type, $price, $isStandard, $floorage, $floor, $subdistrict, $buildingNum, $describe, $phoneNum, $weixin, $images)
    {
        try {
            $carPlace = CarPlace::create([
                'uid' => $uid,
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
                ->leftjoin('user as u1', 'u1.id', '=', 'circle_comments.reply_uid')
                ->select('car_place_comments.id', 'car_place_comments.uid', 'u.username', 'u.avatar', 'u1.username as reply_username',
                    'u1.avatar as reply_avatar', 'car_place_comments.content', 'car_place_comments.car_place_id')
                ->where('car_place_comments.car_place_id', $carPlaceId)
                ->get()->toArray();
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
            CarPlaceComment::where('id', '=', $id)->where('uid', '=', $uid)->delete();
            return true;
        } catch (\Exception $e) {
            throw new CarPlaceException('DELETE_COMMENT_ERROR');
        }
    }

}