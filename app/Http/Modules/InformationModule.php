<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\CommonConstant;
use Dolphin\Ting\Http\Constant\ImageConstant;
use Dolphin\Ting\Http\Exception\InformationException;
use Dolphin\Ting\Http\Model\Information;
use Dolphin\Ting\Http\Utils\Help;
use Exception;
use Psr\Container\ContainerInterface as Container;

class InformationModule extends Module
{
    protected $redis;
    private $appid;
    private $secret;
    private $openid;

    public function __construct(Container $container)
    {
        $this->redis = $container->get('Cache');
        $this->appid = $container->get('Config')['weixin']['program']['appid'];
        $this->secret = $container->get('Config')['weixin']['program']['secret'];
        $this->openid = $container->get('Config')['weixin']['program']['openid'];
    }

    /**
     * 发布动态信息
     *
     * @param $uid
     * @param $title
     * @param $content
     * @param $images
     * @param $subdistrictId
     * @param $subdistrict
     * @param $address
     * @param $gpsAddress
     * @param $lat
     * @param $lng
     * @return bool
     * @throws InformationException
     */
    public function add($uid, $title, $content, $images, $subdistrictId,
            $subdistrict, $address, $gpsAddress, $lat, $lng): bool
    {
        try {
            Information::create([
                'uid' => $uid,
                'title' => $title,
                'content' => $content,
                'status' => CommonConstant::OFF_SHELVES,
                'images' => $images,
                'subdistrict_id' => $subdistrictId,
                'subdistrict' => $subdistrict,
                'address' => $address,
                'gps_address' => $gpsAddress,
                'lat' => $lat,
                'lng' => $lng
            ]);
        } catch (Exception $e) {
            throw new InformationException('ADD_INFORMATION_DATA_ERROR');
        }
        return true;
    }

    /**
     * 删除动态内容
     *
     * @param $uid
     * @param $id
     * @return bool
     * @throws InformationException
     */
    public function delete($uid, $d)
    {
        try {
            Information::where('id', $d)->where('uid', $uid)->delete();
        } catch (\Exception $e) {
            throw new InformationException('DELETE_INFORMATION_ERROR');
        }
        return true;
    }

    /**
     * 获取动态数据列表
     *
     * @param int     $uid
     * @param int     $start      起始位置
     * @param boolean $isPullDown 下拉刷新
     * @param int     $limit      限制条数
     *
     * @return array
     *
     * @author xbantcl
     * @date   2021/3/2 15:32
     */
    public function getList($uid, $start, $isPullDown = false, $limit = 10): array
    {
        $query = Information::leftjoin('user as u', 'u.id', '=', 'informations.uid')
            ->select('u.id', 'u.username', 'u.avatar', 'informations.id as post_id', 'informations.content',
                'informations.title', 'informations.images', 'informations.created_at', 'informations.address',
                'informations.lat', 'informations.lng')
            ->where('informations.status', CommonConstant::ON_SHELVES)
            ->orderBy('informations.sort', 'DESC');
        if ($start > 0) {
            if ($isPullDown) {
                $query->where('informations.id', '>', $start);
            } else {
                $query->where('informations.id', '<', $start);
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
            $start = current($data)['post_id'];
        } else {
            $start = end($data)['post_id'];
        }
        foreach ($data as &$item) {
            $item['images'] = array_map(function ($image) {
                return ImageConstant::BASE_IMAGE_URL . $image;
            }, explode('|', $item['images']));
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
        }
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * 获取用户动态列表
     *
     * @param $uid
     * @param $start
     * @param $limit
     * @param bool $isAdmin
     * @return array
     */
    public function getListByUid($uid, $start, $limit, $isAdmin = false)
    {
        $query = Information::select('id', 'content', 'images', 'created_at')
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
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
        }
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * @param $id
     * @param $status
     * @return bool
     * @throws InformationException
     */
    public function changeStatus($id, $status)
    {
        try {
            Information::where('id', $id)->update(['status' => $status]);
        } catch (\Exception $e) {
            throw new InformationException('UPDATE_INFORMATION_ERROR');
        }
        return true;
    }
}