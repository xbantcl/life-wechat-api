<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;

use Dolphin\Ting\Http\Exception\PincheException;
use Dolphin\Ting\Http\Model\Pinche;
use Dolphin\Ting\Http\Utils\Geohash;
use Exception;

class PincheModule extends Module
{
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
                        $condition, $price, $username, $mobile, $sex, $images, $seatNum, $startTime): bool
    {
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
                'status' => 2
            ]);
        } catch (Exception $e) {
            throw new PincheException('ADD_PINCHE_DATA_ERROR');
        }
        return true;
    }

    public function getList($type, $departureLat, $departureLng, $destinationLat, $destinationLng)
    {
        $geohash = new Geohash();
        if ($departureLat) {
            $departureGeohash = $geohash->encode($departureLat, $departureLng);
        }
        if ($destinationLat) {
            $destinationGeohash = $geohash->encode($destinationLat, $destinationLng);
        }
        try {
            $pinche = Pinche::leftjoin('user as u', 'u.id'. '=', 'pinche.uid')
                ->select('pinche.type', 'pinche.departure_address', 'pinche.destination_address', 'pinche.departure_lat',
                'pinche.departure_lng', 'pinche.destination_lat', 'pinche.destination_lng', 'pinche.price', 'pinche.username',
                'pinche.images', 'pinche.seat_num', 'pinche.start_time');

            if ($departureLat) {
                $pinche->where('pinche.departure_geohash', 'like', substr($departureGeohash, 0, 6));
            }
            if ($destinationLat) {
                $pinche->where('pinche.destination_geohash', 'like', substr($destinationGeohash, 0, 6));
            }
            if ($type !== 'all') {
                $pinche->where('pinche.type', '=', $type);
            }
            $data = $pinche->get()->toArray();
            foreach ($data as $index => &$item) {
                $item['dpt_distance'] = $geohash->getDistance($departureLat, $departureLng, $item['departure_lat'], $item['departure_lng']);
                $item['images'] = explode(',', $item['images'])[0];
            }
            return $data;
        } catch (\Exception $e) {
            throw new PincheException('GET_PINCHE_DATA_ERROR');
        }
    }
}