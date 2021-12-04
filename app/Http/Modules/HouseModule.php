<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Exception\HouseException;
use Dolphin\Ting\Http\Exception\RiskyException;
use Dolphin\Ting\Http\Model\CarPlace;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Model\House;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;

class HouseModule extends Module
{
    private $openid;
    public function __construct(Container $container)
    {
        parent::__construct($container);
        $this->openid = $container->get('Config')['weixin']['program']['openid'];
    }

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
     * @throws HouseException
     */
    public function add($uid, $type, $price, $elevator, $floorage, $floor,
                        $subdistrict, $houseLayout, $houseType, $direction, $decorate, $describe, $mobile, $images)
    {
        try {
            if ($describe) {
                $accessToken = CacheModule::getInstance($this->container)->getAccessToken();
                $res = Help::secCheckContent($accessToken, $this->openid, 2, $describe);
                if ($res !== 'pass') {
                    throw new RiskyException('COMMENT_NOT_PASS');
                }
            }
            $house = House::create([
                'uid' => $uid,
                'subdistrict_id' => 1,
                'type' => $type,
                'price' => $price,
                'elevator' => $elevator,
                'floorage' => $floorage,
                'floor' => $floor,
                'subdistrict' => $subdistrict,
                'direction' => $direction,
                'house_layout' => $houseLayout,
                'house_type' => $houseType,
                'decorate' => $decorate,
                'describe' => $describe,
                'mobile' => $mobile,
                'images' => $images
            ]);
        } catch (RiskyException $e) {
            throw new RiskyException('COMMENT_NOT_PASS');
        } catch (\Exception $e) {
            throw new HouseException('ADD_HOUSE_ERROR');
        }
        return $house->id;
    }

    /**
     * 获取房子列表
     *
     * @param $start
     * @param $type
     * @param bool $isPullDown
     * @param int $limit
     * @return array
     * @throws HouseException
     */
    public function getList($start, $type, $isPullDown = false, $limit = 5)
    {
        try {
            $query = House::where('post_status', '=', CommonConstant::ON_SHELVES)
                ->select('id', 'type', 'house_layout', 'floor', 'price', 'subdistrict', 'images', 'updated_at')
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
            $data = array_map(function ($item) use ($data) {
                $item['thumb'] = ImageConstant::BASE_IMAGE_URL . current(explode('|', $item['images']));
                $item['updated_at'] = Help::timeAgo(strtotime($item['updated_at']));
                unset($item['images']);
                return $item;
            }, $data);
            return ['start' => $start, 'more' => $more, 'list' => $data];
        } catch (\Exception $e) {
            throw new HouseException('GET_HOUSE_LIST_ERROR');
        }
    }

    public function getListByUid($uid, $start, $limit)
    {
        $query = House::select('id', 'type', 'floor', 'price', 'subdistrict', 'images', 'created_at')
            ->orderBy('id', 'DESC');
        if ($uid !== 1) {
            $query->where('uid', $uid);
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
            $data['images'] = array_map(function ($image) {
                return ImageConstant::BASE_IMAGE_URL . $image;
            }, explode('|', $data['images']));
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
        }
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * 获取房屋详情
     *
     * @param $id
     * @return mixed
     * @throws HouseException
     */
    public function detail($id)
    {
        try {
            $data = House::select('id', 'type', 'elevator', 'floor', 'uid', 'floorage', 'price', 'subdistrict',
                'images', 'direction', 'mobile', 'decorate', 'house_type', 'house_layout', 'updated_at', 'describe', 'weixin')
                ->where('post_status', '=', CommonConstant::ON_SHELVES)
                ->where('id', $id)
                ->first()->toArray();
            if (!empty($data)) {
                $data['updated_at'] = date('Y-m-d', strtotime($data['updated_at']));
                if ($data['type'] == '出售') {
                    $data['price'] = $data['price'] . '万';
                } else {
                    $data['price'] = $data['price'] . '元/月';
                }
                $data['images'] = array_map(function ($image) {
                    return ImageConstant::BASE_IMAGE_URL . $image;
                }, explode('|', $data['images']));
            }
            return $data;
        } catch (\Exception $e) {
            throw new HouseException('GET_HOUSE_DETAIL_ERROR');
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
     * @throws HouseException
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
            throw new HouseException('ADD_CAR_PLACE_COMMENT_ERROR');
        }
        return $carPlaceComment->id;
    }

    /**
     * 获取车位评论列表
     *
     * @param int $carPlaceId 车位id
     * @return mixed
     * @throws HouseException
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
            throw new HouseException('GET_COMMENTS_ERROR');
        }
    }

    /**
     * 删除评论
     *
     * @param $uid
     * @param $id
     * @return bool
     * @throws HouseException
     */
    public function deleteComment($uid, $id)
    {
        try {
            CarPlaceComment::where('id', $id)->where('uid', $uid)->delete();
            return true;
        } catch (\Exception $e) {
            throw new HouseException('DELETE_COMMENT_ERROR');
        }
    }

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @param $status
     * @return mixed
     * @throws HouseException
     */
    public function changeStatus($uid, $id, $status)
    {
        try {
            if ($uid != 1) {
                House::where('id', $id)->where('uid', $uid)->update([
                    'post_status' => $status
                ]);
            } else {
                if ($status == 1) {
                    $status = CommonConstant::ADMIN_OFF_SHELVES;
                }
                House::where('id', $id)->update([
                    'post_status' => $status
                ]);
            }
            return true;
        } catch (\Exception $e) {
            throw new HouseException('CHANGE_DATA_STATUS_ERROR');
        }
    }

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @return mixed
     * @throws HouseException
     */
    public function delete($uid, $id)
    {
        try {
            if ($uid != 1) {
                House::where('id', $id)->where('uid', $uid)->delete();
            } else {
                House::where('id', $id)->delete();
            }
            return true;
        } catch (\Exception $e) {
            throw new HouseException('DELETE_DATA_ERROR');
        }
    }
}