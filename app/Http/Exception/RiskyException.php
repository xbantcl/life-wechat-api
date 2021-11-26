<?php

namespace Dolphin\Ting\Http\Exception;

class RiskyException extends CommonException
{
    protected $exceptionCode = 300;
    protected $exception     = [
        'COMMENT_NOT_PASS' => [305, '请注意合法文明用语！'],
    ];
}