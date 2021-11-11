<?php

namespace Dolphin\Ting\Http\Exception;

class CircleException extends CommonException
{
    protected $exceptionCode = 300;
    protected $exception     = [
        'ADD_CIRCLE_DATA_ERROR'    => [301, '发布圈子数据失败'],
        'ADD_CIRCLE_COMMENT_ERROR' => [302, '发布圈子评论失败'],
        'DELETE_CIRCLE_DATA_ERROR' => [303, '删除圈子失败'],
        'DELETE_CIRCLE_COMMENT_ERROR' => [304, '删除圈子评论失败'],
        'CIRCLE_COMMENT_NOT_PASS' => [305, '请注意合法文明用语！'],
    ];
}