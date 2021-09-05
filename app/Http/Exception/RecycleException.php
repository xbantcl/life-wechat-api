<?php

namespace Dolphin\Ting\Http\Exception;

class RecycleException extends CommonException
{
    protected $exceptionCode = 800;
    protected $exception     = [
        'ADD_RECYCLE_DATA_ERROR' => [801, '添加回收预约失败'],
        'GET_RECYCLE_LIST_ERROR' => [802, '获取回收预约列表失败'],
        'CANCLE_RECYCLE_ERROR'   => [803, '取消回收预约失败'],
    ];
}