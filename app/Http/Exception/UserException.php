<?php

namespace Dolphin\Ting\Http\Exception;

class UserException extends CommonException
{
    protected $exceptionCode = 101;
    protected $exception     = [
        'USERNAME_NON_EXIST'  => [101, '用户不存在'],
        'PASSWORD_ERROR'      => [102, '用户密码错误'],
        'REGISTER_USER_ERROR' => [103, '用户注册失败'],
        'WEIXIN_LOGIN_ERROR'  => [104, '微信授权登录失败'],
        'LOGIN_ERROR'         => [105, '用户登录失败']
    ];
}