<?php

namespace Dolphin\Ting\Http\Exception;

class RentException extends CommonException
{
    protected $exceptionCode = 600;
    protected $exception     = [
        'ADD_RENT_DATA_ERROR'         => [601, '发布租借数据失败'],
        'GET_CAR_PLACE_LIST_ERROR'    => [602, '获取车位列表失败'],
        'ADD_CAR_PLACE_COMMENT_ERROR' => [603, '发布车位评论失败'],
        'GET_CAR_PLACE_DETAIL_ERROR'  => [604, '获取车位详情失败'],
        'GET_COMMENTS_ERROR'          => [605, '获取车位评论失败'],
    ];
}