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
                'condition' => $condition,
                'price' => $price,
                'username' => $username,
                'mobile' => $mobile,
                'sex' => $sex,
                'images' => $images,
                'seat_num' => $seatNum,
                'start_time' => $startTime
            ]);
        } catch (Exception $e) {
            throw new PincheException('ADD_PINCHE_DATA_ERROR');
        }
        return true;
    }
}