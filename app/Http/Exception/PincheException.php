<?php

namespace Dolphin\Ting\Http\Exception;

class PincheException extends CommonException
{
    protected $exceptionCode = 500;
    protected $exception     = [
        'ADD_PINCHE_DATA_ERROR'    => [501, '发布拼车信息失败'],
        'GET_PINCHE_DATA_ERROR'    => [502, '获取拼车信息失败'],
    ];
}