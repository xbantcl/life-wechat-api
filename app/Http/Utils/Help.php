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
        $params = [];
        if ($request->getParsedBody()) {
            if (!is_array($request->getParsedBody())) {
                $params = json_decode($request->getParsedBody());
            } else {
                $params = $request->getParsedBody();
            }
        }
        if ($uid) {
            return array_merge($params, ['uid' => $uid]);
        }
        return $params;
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

    /**
     * 计算过去多久
     *
     * @param $posttime
     * @return string
     */
    public static function timeAgo($posttime)
    {
        //当前时间的时间戳
        $nowtimes = strtotime(date('Y-m-d H:i:s'),time());
        //之前时间参数的时间戳
        $posttimes = strtotime($posttime);
        //相差时间戳
        $counttime = $nowtimes - $posttime;
        //进行时间转换
        if($counttime<=10){
            return '刚刚';
        }else if($counttime>10 && $counttime<=30){
            return '刚才';
        }else if($counttime>30 && $counttime<=60){
            return '刚一会';
        }else if($counttime>60 && $counttime<=120){
            return '1分钟前';
        }else if($counttime>120 && $counttime<=180){
            return '2分钟前';
        }else if($counttime>180 && $counttime<3600){
            return intval(($counttime/60)).'分钟前';
        }else if($counttime>=3600 && $counttime<3600*24){
            return intval(($counttime/3600)).'小时前';
        }else if($counttime>=3600*24 && $counttime<3600*24*2){
            return '昨天';
        }else if($counttime>=3600*24*2 && $counttime<3600*24*3){
            return '前天';
        }else if($counttime>=3600*24*3 && $counttime<=3600*24*20){

            return intval(($counttime/(3600*24))).'天前';

        }else{
            return $posttime;
        }
    }
}