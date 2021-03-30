<?php

namespace Dolphin\Ting\Http\Exception;

class SecondhandGoodsException extends CommonException
{
    protected $exceptionCode = 500;
    protected $exception     = [
        'ADD_SECONDHAND_GOODS_ERROR'        => [501, '发布商品失败'],
        'GET_SECONDHAND_GOODS_LIST_ERROR'   => [502, '获取商品列表失败'],
        'GET_SECONDHAND_GOODS_DETAIL_ERROR' => [503, '获取商品详情失败']
    ];
}