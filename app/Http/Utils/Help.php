<?php namespace Dolphin\Ting\Http\Utils;

use Ahc\Jwt\JWT;
use Ahc\Jwt\JWTException;
use function GuzzleHttp\Psr7\str;

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

    /**
     * 获取token
     * @param array $payload
     */
    public static function getToken(array $payload)
    {
        return (new JWT('@3imd53AcdD.%#j', 'HS512', 3600 * 24 * 90))->encode($payload);
    }

    /**
     * 验证token
     * @param string $token
     * @return array|bool
     */
    public static function decode($token)
    {
        try {
            $payload = (new JWT('@3imd53AcdD.%#j', 'HS512', 3600 * 24 * 90))->decode($token);
        } catch (JWTException $e) {
            return false;
        }
        return $payload;
    }

    public static function fdate($time) {
        if (!$time) return false;
        $fdate = '';
        $d = time() - intval($time);
        $ld = time() - mktime(0, 0, 0, 0, 0, date('Y')); //年
        $md = time() - mktime(0, 0, 0, date('m'), 0, date('Y')); //月
        $byd = time() - mktime(0, 0, 0, date('m'), date('d') - 2, date('Y')); //前天
        $yd = time() - mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')); //昨天
        $dd = time() - mktime(0, 0, 0, date('m'), date('d'), date('Y')); //今天
        $td = time() - mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')); //明天
        $atd = time() - mktime(0, 0, 0, date('m'), date('d') + 2, date('Y')); //后天
        if ($d == 0) {
            $fdate = '刚刚';
        } else {
            switch ($d) {
                case $d < $atd:
                    $fdate = date('Y年m月d日 H:i', $time);
                    break;
                case $d < $td:
                    $fdate = '后天' . date('H:i', $time);
                    break;
                case $d < $dd:
                    $fdate = '今天' . date('H:i', $time);
                    break;
                case $d < 0:
                    $fdate = '明天' . date('H:i', $time);
                    break;
                case $d < 60:
                    $fdate = $d . '秒前';
                    break;
                case $d < 3600:
                    $fdate = floor($d / 60) . '分钟前';
                    break;
                case $d < $yd:
                    $fdate = '昨天' . date('H:i', $time);
                    break;
                case $d < $byd:
                    $fdate = '前天' . date('H:i', $time);
                    break;
                case $d < $md:
                    $fdate = date('m-d H:i', $time);
                    break;
                case $d < $ld:
                    $fdate = date('m-d H:i', $time);
                    break;
                default:
                    $fdate = date('Y-m-d', $time);
                    break;
            }
        }
        return $fdate;
    }

    public static function cityNameToPinyin($name) {
        if (strpos($name, '陕西') !== false) {
            return 'Shanxis.php';
        } elseif (strpos($name, '四川') !== false) {
            return 'Sichuan.php';
        } elseif (strpos($name, '山西') !== false) {
            return 'Shanxi.php';
        } elseif (strpos($name, '内蒙古') !== false) {
            return 'Neimenggu.php';
        } elseif (strpos($name, '江西') !== false) {
            return 'Jiangxi.php';
        } elseif (strpos($name, '辽宁') !== false) {
            return 'Liaoling.php';
        } elseif (strpos($name, '宁夏回族') !== false) {
            return 'Ningxia.php';
        } elseif (strpos($name, '青海') !== false) {
            return 'Qinhai.php';
        } elseif (strpos($name, '山东') !== false) {
            return 'Shandong.php';
        } elseif (strpos($name, '上海') !== false) {
            return 'Shanghai.php';
        } elseif (strpos($name, '天津') !== false) {
            return 'Tianjing.php';
        } elseif (strpos($name, '新疆维吾尔') !== false) {
            return 'Xinjiang.php';
        } elseif (strpos($name, '云南') !== false) {
            return 'Yunnan.php';
        } elseif (strpos($name, '浙江') !== false) {
            return 'Zhejiang.php';
        } elseif (strpos($name, '甘肃') !== false) {
            return 'Gansu.php';
        } elseif (strpos($name, '西藏') !== false) {
            return 'Xizang.php';
        } elseif (strpos($name, '吉林') !== false) {
            return 'Jiling.php';
        } elseif (strpos($name, '安徽') !== false) {
            return 'Anhui.php';
        } elseif (strpos($name, '北京') !== false) {
            return 'Beijing.php';
        } elseif (strpos($name, '重庆') !== false) {
            return 'Chongqin.php';
        } elseif (strpos($name, '福建') !== false) {
            return 'Fujian.php';
        } elseif (strpos($name, '广东') !== false) {
            return 'Guangdong.php';
        } elseif (strpos($name, '广西壮族') !== false) {
            return 'Guangxi.php';
        } elseif (strpos($name, '贵州') !== false) {
            return 'Guizhou.php';
        } elseif (strpos($name, '海南') !== false) {
            return 'Hainan.php';
        } elseif (strpos($name, '河北') !== false) {
            return 'Hebei.php';
        } elseif (strpos($name, '黑龙江') !== false) {
            return 'Heilongjiang.php';
        } elseif (strpos($name, '河南') !== false) {
            return 'Henan.php';
        } elseif (strpos($name, '湖北') !== false) {
            return 'Hubei.php';
        } elseif (strpos($name, '湖南') !== false) {
            return 'Hunan.php';
        } elseif (strpos($name, '江苏') !== false) {
            return 'Jiangshu.php';
        } else {
            return false;
        }
    }

    /**
     * 获取城市id
     *
     * @param $address
     * @return bool|mixed
     */
    public static function getCityId($address)
    {
        $isLevel2 = false;
        $provence = explode('省', $address);
        if (count($provence) === 1) {
            $provence = explode('市', $address);
            if (count($provence) === 1) {
                $provence = explode('自治区', $address);
                if (count($provence) === 1) {
                    return false;
                }
            } else {
                $isLevel2 = true;
            }
        }
        $cities = self::cityNameToPinyin($provence[0]);
        if (!$cities) {
            return false;
        }
        $data = require dirname(__FILE__) . '/City/' . $cities;
        $cityName = '';
        if (!$isLevel2) {
            $city = explode('市', $provence[1]);
            if (count($city) === 1) {
                $city = explode('自治州', $provence[1]);
                if (count($city) === 1) {
                    $city = explode('地区', $provence[1]);
                    if (ount($city) === 1) {
                        return false;
                    }
                    $cityName = $city[0] . '地区';
                } else {
                    $cityName = $city[0] . '自治州';
                }
            } else {
                $cityName = $city[0] . '市';
            }
            $cityData = [];
            foreach ($data['children'] as $item) {
                if (strpos($item['name'], $cityName) !== false) {
                    if (!isset($item['children'])) {
                        return $item['code'];
                    }
                    $cityData = $item['children'];
                    break;
                }
            }
        }
        $qu = explode('区', $city[1]);
        if (count($qu) === 1) {
            $qu = explode('县', $city[1]);
            if (count($qu) === 1) {
                return false;
            }
        }
        foreach ($cityData as $item) {
            if (strpos($item['name'], $qu[0]) !== false) {
                return $item['code'];
            }
        }
    }

    /**
     * 通过经纬度获取位置名称
     *
     * @param $lat
     * @param $lng
     */
    public static function getCityAddressByLatLng($lat, $lng, $mapKey) {
        $res = Curl::get('https://apis.map.qq.com/ws/geocoder/v1/?location=' . $lat . ',' . $lng . '&key=' . $mapKey);
        $res = json_decode($res);
        if (isset($res->status) && $res->status === 0) {
            $addressComponent = $res->result->address_component;
            return $addressComponent->province . $addressComponent->city . $addressComponent->district;
        } else {
            return false;
        }
    }

    /**
     * 获取当前毫秒时间戳
     * @return float
     */
    public static function msectime()
    {
        list($msec, $sec) = explode(' ', microtime());
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        return $msectime;
    }

    /**
     * 格式化时间
     * @param $date
     * @return string
     */
    public static function formatTime($date) {
        $timer = strtotime($date);
        $diff = time() - $timer;
        $day = floor($diff / 86400);
        $free = $diff % 86400;
        if($day > 0) {
            return $day."天前";
        }else{
            if($free>0){
                $hour = floor($free / 3600);
                $free = $free % 3600;
                if($hour>0){
                    return $hour."小时前";
                }else{
                    if($free>0){
                        $min = floor($free / 60);
                        $free = $free % 60;
                        if($min>0){
                            return $min."分钟前";
                        }else{
                            if($free>0){
                                return $free."秒前";
                            }else{
                                return '刚刚';
                            }
                        }
                    }else{
                        return '刚刚';
                    }
                }
            }else{
                return '刚刚';
            }
        }
    }
}

