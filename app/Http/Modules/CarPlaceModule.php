<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CarPlaceConstant;
use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Exception\CarPlaceException;
use Dolphin\Ting\Http\Exception\RiskyException;
use Dolphin\Ting\Http\Model\CarPlace;
use Dolphin\Ting\Http\Model\CarPlaceComment;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;

class CarPlaceModule extends Module
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
     * @param $mobile
     * @param $images
     *
     * @return mixed
     * @throws CarPlaceException
     */
    public function add($uid, $type, $price, $isStandard, $floorage, $floor, $subdistrict, $buildingNum, $describe,
                        $mobile, $images)
    {
        try {
            if ($describe) {
                $accessToken = CacheModule::getInstance($this->container)->getAccessToken();
                $res = Help::secCheckContent($accessToken, $this->openid, 2, $subdistrict.$describe);
                if ($res !== 'pass') {
                    throw new RiskyException('COMMENT_NOT_PASS');
                }
            }

            $carPlace = CarPlace::create([
                'uid' => $uid,
                'subdistrict_id' => 1,
                'type' => $type,
                'price' => $price,
                'post_status' => CommonConstant::AUDIT,
                'is_standard' => $isStandard,
                'floorage' => $floorage,
                'floor' => $floor,
                'subdistrict' => $subdistrict,
                'building_number' => $buildingNum,
                'describe' => $describe,
                'mobile' => $mobile,
                'images' => $images
            ]);
        } catch (RiskyException $e) {
            throw new RiskyException('COMMENT_NOT_PASS');
        } catch (\Exception $e) {
            throw new CarPlaceException('ADD_CAR_PLACE_ERROR');
        }
        return $carPlace->id;
    }

    /**
     * 更新图片数据
     *
     * @param $uid
     * @param $id
     * @param $image
     * @param string $type
     * @throws CarPlaceException
     */
    public function updateImage($uid, $id, $image, $type = 'add')
    {
        try {
            $obj = CarPlace::select('images')->where('id', $id)->where('uid', $uid)->first();
            $images = '';
            if ($type == 'delete') {
                $temp = explode('|', $obj->images);
                $key = array_search($image, $temp);
                if ($key !== false) {
                    array_splice($temp, $key, 1);
                    $images = implode('|', $temp);
                }
            } elseif ($type == 'add') {
                $images = $obj->images . '|' . $image;
            } else {
                $images = false;
            }
            if ($images !== false) {
                CarPlace::where('id', $id)->where('uid', $uid)->update([
                    'images' => $images
                ]);
            }
        } catch (\Exception $e) {
            throw new CarPlaceException('UPDATE_ERROR');
        }
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
            $query = CarPlace::where('post_status', CarPlaceConstant::ON_SHELVES)
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
                $item['thumb'] = ImageConstant::BASE_IMAGE_URL . current(explode('|', $item['images']));
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

    public function getListByUid($uid, $start, $limit)
    {
        $query = CarPlace::select('id', 'type', 'post_status', 'is_standard', 'floor', 'price', 'subdistrict', 'images', 'building_number', 'updated_at')
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
            $item['images'] = array_map(function ($image) {
                return ImageConstant::BASE_IMAGE_URL . $image;
            }, explode('|', $item['images']));
            $item['updated_at'] = date('Y-m-d', strtotime($item['updated_at']));
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
                'subdistrict', 'images', 'building_number', 'mobile', 'updated_at', 'describe', 'weixin')
                ->where('id', $id)
                ->where('post_status', CommonConstant::ON_SHELVES)
                ->first();
            if ($data instanceof CarPlace) {
                $data = $data->toArray();
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
                $data['images'] = array_map(function ($image) {
                    return ImageConstant::BASE_IMAGE_URL . $image;
                }, explode('|', $data['images']));
            } else {
                $data = [];
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

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @param $status
     * @return mixed
     * @throws CarPlaceException
     */
    public function changeStatus($uid, $id, $status)
    {
        try {
            if ($uid != 1) {
                CarPlace::where('id', $id)->where('uid', $uid)->update([
                    'post_status' => $status
                ]);
            } else {
                if ($status == 1) {
                    $status = CommonConstant::ADMIN_OFF_SHELVES;
                }
                CarPlace::where('id', $id)->update([
                    'post_status' => $status
                ]);
            }
            return true;
        } catch (\Exception $e) {
            throw new CarPlaceException('CHANGE_DATA_STATUS_ERROR');
        }
    }

    /**
     * 更改数据状态
     * @param $uid
     * @param $id
     * @return mixed
     * @throws CarPlaceException
     */
    public function delete($uid, $id)
    {
        try {
            if ($uid != 1) {
                CarPlace::where('id', $id)->where('uid', $uid)->delete();
            } else {
                CarPlace::where('id', $id)->delete();
            }
            return true;
        } catch (\Exception $e) {
            throw new CarPlaceException('DELETE_DATA_ERROR');
        }
    }
}