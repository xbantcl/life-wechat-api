<?php

namespace Dolphin\Ting\Http\Exception;

class CarPlaceException extends CommonException
{
    protected $exceptionCode = 400;
    protected $exception     = [
        'ADD_CAR_PLACE_ERROR'         => [401, '发布车位数据失败'],
        'GET_CAR_PLACE_LIST_ERROR'    => [402, '获取车位列表失败'],
        'ADD_CAR_PLACE_COMMENT_ERROR' => [403, '发布车位评论失败'],
    ];
}