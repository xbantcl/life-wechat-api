<?php namespace Dolphin\Ting\Http\Utils;

class Help
{
    /**
     * 获取密码盐值.
     *
     * @return string
     */
    public static function getSalt()
    {
        return substr(md5(\uniqid(mt_rand(1, 10000000))), 0, 11);
    }

    /**
     * 加密密码.
     *
     * @param string $password
     * @param string $salt
     *
     * @return string
     */
    public static function encryptPassword($password, $salt)
    {
        return md5(md5($password) . $salt);
    }

    public static function isPhone($account)
    {
        return preg_match('/^1[3|5|7|8|][0-9]{9}/', $account);
    }

    public static function isEmail($account)
    {
        return preg_match('#[A-Z0-9a-z._%+-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,4}#', $account);
    }

    public static function getRandomStr($length = 32)
    {
        $charset = 'abcdefghijklmnopkrstuvwhyzABCDEFGHIJKLMNOPKRSTUVWHYZ0123456789~!@#$%^&*';
        $charset = str_shuffle($charset);
        return substr($charset, 0, $length);
    }

    public static function response($response, $data = null, $code = 0, $message = 'success') {
        if (isset($data['code'])) {
            $code = $data['code'];
            $message = $data['message'];
            $data = null;
        }
        return $response->withJson(['error_code' => $code, 'message' => $message, 'data'=> $data]);
    }

    public static function formatResponse($code, $message)
    {
        return ['code' => $code, 'message' => $message];
    }

    public static function getParams($request, $uid = 0) {
        if ($uid) {
            return array_merge($request->getParsedBody(), ['uid' => $uid]);
        }
        return array_merge($request->getParsedBody());
    }

    public static function config($key)
    {
        $configName = __DIR__ . '/../config.php';
        if (!file_exists($configName)) {
            return false;
        }
        $configs = require $configName;
        if (!isset($configs[$key])) {
            return false;
        }
        return $configs[$key];
    }

    /**
     * 获取uuid.
     */
    public static function getUuid()
    {
        mt_srand((double)microtime() * 10000);
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = chr(123) . substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12)
            . chr(125);
        return $uuid;
    }

    public static function translateAvatar(array &$data)
    {
        if (!empty($data['avatar'])) {
            $avatarDomin = Help::config('qiniu')['bucket']['pub']['avatar'];
            $data['avatar'] = $avatarDomin . $data['avatar'];
        }
    }
}