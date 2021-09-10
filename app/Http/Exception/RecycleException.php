<?php

namespace Dolphin\Ting\Http\Exception;

class RecycleException extends CommonException
{
    protected $exceptionCode = 800;
    protected $exception     = [
        'ADD_RECYCLE_DATA_ERROR'         => [801, '添加回收预约失败'],
        'GET_RECYCLE_ORDER_LIST_ERROR'   => [802, '获取回收预约列表失败'],
        'CANCLE_RECYCLE_ERROR'           => [803, '取消回收预约失败'],
        'UPDATE_RECYCLE_DATA_ERROR'      => [804, '更新回收预约失败'],
        'GET_RECYCLE_ORDER_DETAIL_ERROR' => [805, '获取回收预约详情失败'],
        'DELETE_RECYCLE_ORDER_ERROR'     => [806, '删除回收预约详情失败']
    ];
}