<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;
use Dolphin\Ting\Http\Exception\UserException;

use Dolphin\Ting\Http\Model\Circle;
use Exception;
use Dolphin\Ting\Http\Modules\Module;
use Psr\Container\ContainerInterface as Container;

class CircleModule extends Module
{
    /**
     * 发布圈子动态
     *
     * @param UserIdRequest $request
     *
     * @return UserResponse
     *
     * @throws UserException
     *
     * @author xbantcl
     * @date   2021/2/22 9:32
     */
    public function add($uid, $content, $images = '')
    {
        try {
            Circle::create([
                'uid' => 1,
                'content' => 'test',
                'images' => 'a|b|c',
                'create_time' => time(),
                'modify_time' => time()
            ]);
        } catch (Exception $e) {
            throw new CircleException('ADD_CIRCLE_DATA_ERROR');
        }

        return true;
    }
}