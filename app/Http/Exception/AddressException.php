<?php

namespace Dolphin\Ting\Http\Exception;

class AddressException extends CommonException
{
    protected $exceptionCode = 700;
    protected $exception     = [
        'ADD_ADDRESS_DATA_ERROR'    => [701, '添加地址数据失败'],
        'GET_ADDRESS_LIST_ERROR'    => [702, '获取地址列表失败'],
        'GET_ADDRESS_DETAIL_ERROR'  => [703, '获取地址详情失败'],
        'DELETE_ADDRESS_ERROR'      => [704, '删除地址信息失败'],
        'UPDATE_ADDRESS_ERROR'      => [705, '更新地址信息失败']
    ];
}