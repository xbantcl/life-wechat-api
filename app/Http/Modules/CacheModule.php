<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CacheException;
use Dolphin\Ting\Http\Utils\Help;
use Exception;
use Psr\Container\ContainerInterface as Container;

class CacheModule extends Module
{
    protected $redis;
    private $appid;
    private $secret;
    private $openid;

    public function __construct(Container $container)
    {
        $this->redis = $container->get('Cache');
        $this->appid = $container->get('Config')['weixin']['program']['appid'];
        $this->secret = $container->get('Config')['weixin']['program']['secret'];
        $this->openid = $container->get('Config')['weixin']['program']['openid'];
    }

    /**
     * 获取access_token
     *
     * @return bool|mixed
     * @throws CacheException
     */
    public function getAccessToken()
    {
        try {
            $accessToken = $this->redis->get('access_token');
            if ($accessToken) {
                return $accessToken;
            }
            $accessToken = Help::getAccessToken($this->openid, $this->secret);
            if ($accessToken) {
                $this->redis->set('access_token', $accessToken, 7000);
                return $accessToken;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new CacheException('GET_ACCESSTOKEN_ERROR');
        }
    }
}