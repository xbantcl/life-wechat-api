<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;

use Dolphin\Ting\Http\Exception\PincheException;
use Dolphin\Ting\Http\Model\Pinche;
use Dolphin\Ting\Http\Utils\Geohash;
use Dolphin\Ting\Http\Utils\Help;
use Exception;
use Psr\Container\ContainerInterface as Container;

class PincheModule extends Module
{
    private $mapKey;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->mapKey = $container->get('Config')['weixin']['program']['map_key'];
    }
    /**
     *
     * @param $uid
     * @param $type
     * @param $departureAddress
     * @param $destinationAddress
     * @param $departureLat
     * @param $departureLng
     * @param $destinationLat
     * @param $destinationLng
     * @param $condition
     * @param $price
     * @param $username
     * @param $mobile
     * @param $sex
     * @param $images
     * @return bool
     * @throws CircleException
     *
     * @author xbantcl
     * @date   2021/4/6 15:32
     */
    public function add($uid, $type, $departureAddress, $destinationAddress, $departureLat, $departureLng, $destinationLat, $destinationLng,
                        $condition, $price, $username, $mobile, $sex, $images, $seatNum, $startTime, $departureName, $destinationName): bool
    {
        $dptCityId = Help::getCityId($departureAddress);
        if (!$dptCityId) {
            $dptAddress = Help::getCityAddressByLatLng($departureLat, $destinationLng, $this->mapKey);
            if ($dptAddress) {
                $dptCityId = Help::getCityId($dptAddress);
            } else {
                throw new PincheException('GET_ADDRESS_ERROR');
            }
        }
        $dstCityId = Help::getCityId($destinationAddress);
        if (!$dstCityId) {
            $dstAddress = Help::getCityAddressByLatLng($destinationLat, $destinationLng, $this->mapKey);
            if ($dstAddress) {
                $dstCityId = Help::getCityId($dstAddress);
            } else {
                throw new PincheException(GET_ADDRESS_ERROR);
            }
        }
        try {
            $geohash = new Geohash();
            $departureGeohash = $geohash->encode($departureLat, $departureLng);
            $destinationGeohash = $geohash->encode($destinationLat, $destinationLng);
            Pinche::create([
                'uid' => $uid,
                'type' => $type,
                'departure_geohash' => $departureGeohash,
                'destination_geohash' => $destinationGeohash,
                'departure_address' => $departureAddress,
                'destination_address' => $destinationAddress,
                'departure_lat' => $departureLat,
                'departure_lng' => $departureLng,
                'destination_lat' => $destinationLat,
                'destination_lng' => $destinationLng,
                'condition' => $condition,
                'price' => $price,
                'username' => $username,
                'mobile' => $mobile,
                'sex' => $sex,
                'images' => $images,
                'seat_num' => $seatNum,
                'start_time' => strtotime($startTime),
                'status' => 2,
                'departure_name' => $departureName,
                'destination_name' => $destinationName,
                'departure_city_id' => $dptCityId,
                'destination_city_id' => $dstCityId
            ]);
        } catch (Exception $e) {
            throw new PincheException('ADD_PINCHE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 获取拼车信息
     *
     * @param $type
     * @param $departureLat
     * @param $departureLng
     * @param $destinationLat
     * @param $destinationLng
     * @param $dptId
     * @param $dstId
     *
     * @return mixed
     * @throws PincheException
     */
    public function getList($type, $departureLat, $departureLng, $destinationLat, $destinationLng, $dptId, $dstId, $searchType = 'location')
    {
        $geohash = new Geohash();
        if ($searchType === 'location' && $departureLat) {
            $departureGeohash = $geohash->encode($departureLat, $departureLng);
        }
        if ($searchType === 'location' && $destinationLat) {
            $destinationGeohash = $geohash->encode($destinationLat, $destinationLng);
        }
        try {
            $pinche = Pinche::leftjoin('user as u', 'u.id', '=', 'pinche.uid')
                ->select('pinche.id', 'pinche.type', 'pinche.departure_address', 'pinche.destination_address', 'pinche.departure_lat',
                'pinche.departure_lng', 'pinche.destination_lat', 'pinche.destination_lng', 'pinche.price', 'pinche.username',
                'pinche.images', 'pinche.seat_num', 'pinche.start_time')
                ->where('pinche.status', '=', 2);
            if ($type !== 'all') {
                $pinche->where('pinche.type', '=', $type);
            }
            if ($searchType === 'location' && $departureLat) {
                $pinche->where('pinche.departure_geohash', 'like', substr($departureGeohash, 0, 5) . '%');
            } elseif ($dptId) {
                $pinche->where('pinche.departure_city_id', '=', $dptId);
            }
            if ($searchType === 'location' && $destinationLat) {
                $pinche->where('pinche.destination_geohash', 'like', substr($destinationGeohash, 0, 5) . '%');
            } elseif ($dstId) {
                $pinche->where('pinche.destination_city_id', 'like', $dstId);
            }

            $data = $pinche->get()->toArray();
            foreach ($data as $index => &$item) {
                $item['dpt_distance'] = $geohash->getDistance($departureLat, $departureLng, $item['departure_lat'], $item['departure_lng']);
                $item['images'] = explode(',', $item['images']);
                $item['start_time'] = Help::fdate($item['start_time']);
            }
            return $data;
        } catch (\Exception $e) {
            throw new PincheException('GET_PINCHE_DATA_ERROR');
        }
    }

    /**
     * 获取拼车详细信息
     *
     * @param $id
     * @return mixed
     */
    public function detail($id)
    {
        try {
            $pinche = Pinche::leftjoin('user as u', 'u.id', '=', 'pinche.uid')
                ->select('pinche.id', 'pinche.type', 'pinche.departure_name', 'pinche.destination_name', 'pinche.departure_address', 'pinche.destination_address', 'pinche.departure_lat',
                    'pinche.departure_lng', 'pinche.destination_lat', 'pinche.destination_lng', 'pinche.price', 'pinche.username',
                    'pinche.images', 'pinche.seat_num', 'pinche.start_time', 'u.avatar', 'pinche.mobile')
                ->where('pinche.status', '=', 2)
                ->where('pinche.id', '=', $id)
                ->first();
            if ($pinche) {
                $pinche->images = explode(',', $pinche->images);
                $item['start_time'] = Help::fdate($pinche->start_time);
            }
            return $pinche;
        } catch (\Exception $e) {

        }

    }
}