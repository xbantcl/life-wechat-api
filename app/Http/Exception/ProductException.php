<?php

namespace Dolphin\Ting\Http\Exception;

class ProductException extends CommonException
{
    protected $exceptionCode = 900;
    protected $exception     = [
        'ADD_CATEGORY_DATA_ERROR'    => [901, '添加商品分类数据失败'],
        'GET_CATEGORY_LIST_ERROR'    => [902, '获取分类列表失败'],
        'GET_ADDRESS_DETAIL_ERROR'  => [903, '获取地址详情失败'],
        'DELETE_ADDRESS_ERROR'      => [904, '删除地址信息失败'],
        'UPDATE_ADDRESS_ERROR'      => [905, '更新地址信息失败']
    ];
}