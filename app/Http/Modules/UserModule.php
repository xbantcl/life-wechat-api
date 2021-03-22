<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\UserConstant;
use Dolphin\Ting\Http\Exception\UserException;
use Dolphin\Ting\Http\Model\User;
use Dolphin\Ting\Http\Utils\Help;
use Exception;

class UserModule extends Module
{

    /**
     * 用户登录
     *
     * @param $phone
     * @param $password
     * @return mixed
     *
     * @throws UserException
     */
    public function login($phone, $password)
    {
        try {
            $user = User::select('id', 'avatar', 'username', 'salt')
                ->where('phone', '=', $phone)
                ->first();
            if (empty($user)) {
                throw new UserException('USERNAME_NON_EXIST');
            }
            if ($user->password !== Help::encryptPassword($password, $user->salt)) {
                throw new UserException('PASSWORD_ERROR');
            }
            $token = Help::getToken(['uid' => $user->id]);
        } catch (\Exception $e) {
            throw new UserException('USERNAME_NON_EXIST');
        }
        unset($user->salt);
        return ['user' => $user, 'token' => $token];
    }

    /**
     * 用户注册
     * @param string $phone
     * @param string $password
     *
     * @return array
     * @throws UserException
     */
    public function register($phone, $password)
    {
        try {
            $salt = Help::getSalt();
            $avatar = UserConstant::AVATARS[range(0, 5)];
            $username = '温度' . substr(md5(\uniqid(mt_rand(1, 10000000))), 0, 6);
            $user = User::create([
                'phone' => $phone,
                'password' => Help::encryptPassword($password, $salt),
                'avatar' => $avatar,
                'username' => $username
            ]);
            $token = Help::getToken(['uid' => $user->id]);
            return ['user' => ['uid' => $user->id, 'avatar' => $avatar, 'username' => $username], 'token' => $token];
        } catch (Exception $e) {
            throw new UserException('REGISTER_USER_ERROR');
        }
    }
}