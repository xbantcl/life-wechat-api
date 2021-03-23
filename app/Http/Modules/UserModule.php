<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Constant\UserConstant;
use Dolphin\Ting\Http\Exception\UserException;
use Dolphin\Ting\Http\Model\User;
use Dolphin\Ting\Http\Utils\Curl;
use Dolphin\Ting\Http\Utils\Help;
use Dolphin\Ting\Http\Utils\WXBizDataCrypt;
use Psr\Container\ContainerInterface as Container;
use Exception;

class UserModule extends Module
{
    private $accessKey;
    private $secretKey;
    private $imageBucket;
    private $appid;
    private $secret;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->accessKey = $container->get('Config')['qiniu']['accessKey'];
        $this->secretKey = $container->get('Config')['qiniu']['secretKey'];
        $this->imageBucket = 'wendushequ-circle';
        $this->appid = $container->get('Config')['weixin']['program']['appid'];
        $this->secret = $container->get('Config')['weixin']['program']['secret'];

    }
    /**
     * 用户登录
     *
     * @param $phone
     * @param $password
     *
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
            throw new UserException('LOGIN_ERROR');
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
            $avatar = UserConstant::AVATARS[rand(0, 4)];
            $username = '温度' . substr(md5(\uniqid(mt_rand(1, 10000000))), 0, 6);
            $user = User::select('id')
                ->where('phone', '=', $phone)
                ->first();
            if ($user) {
                throw new UserException('USERNAME_EXIST');
            }
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

    /**
     * 微信授权登录
     *
     * @param $code
     * @param $username
     * @param $avatar
     *
     * @return array
     * @throws UserException
     */
    public function wxLogin($code, $username, $avatar)
    {
        try {
            if ('youke' === $code) {
                $uid = 99999999;
                $token = Help::getToken(['uid' => $uid]);
                return ['user' => ['uid' => $uid], 'token' => $token];
            }
            $authWxUrl = 'https://api.weixin.qq.com/sns/jscode2session?appid=' . $this->appid .
                '&secret=' . $this->secret .
                '&js_code=' . $code . '&grant_type=authorization_code';
            $res = json_decode(Curl::get($authWxUrl), true);
            if (isset($res['openid'])) {
                $user = User::select('id', 'avatar', 'username', 'openid')
                    ->where('openid', '=', $res['openid'])
                    ->first();
                if (empty($user)) {
                    $user = User::create([
                        'username' => $username,
                        'avatar' => $avatar,
                        'openid' => $res['openid']
                    ]);
                } else {
                    $user->avatar = $avatar;
                    $user->username = $username;
                    $user->last_sign_in_time = date('Y-m-d H:i:s');
                    $user->save();
                }
                $token = Help::getToken(['uid' => $user->id]);
                return ['user' => ['uid' => $user->id, 'username' => $user->username, 'avatar' => $user->avatar], 'token' => $token];
            } else {
                throw new UserException('WEIXIN_LOGIN_ERROR', [], $res['errmsg']);
            }
        } catch (\Exception $e) {
            throw new UserException('LOGIN_ERROR', [], $e->getMessage());
        }
    }
}