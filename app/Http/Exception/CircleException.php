<?php

namespace Dolphin\Ting\Http\Exception;

class CircleException extends CommonException
{
    protected $exceptionCode = 300;
    protected $exception     = [
        'ADD_CIRCLE_DATA_ERROR'                => [301, '发布圈子数据失败']
    ];
}