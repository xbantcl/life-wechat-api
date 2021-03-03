<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;

use Dolphin\Ting\Http\Model\Circle;
use Exception;
use Dolphin\Ting\Http\Modules\Module;

class CircleModule extends Module
{
    /**
     * 发布圈子动态
     *
     * @param int    $uid     用户id
     * @param string $content 动态类容
     * @param string $images  动态图片
     *
     * @return boolean
     *
     * @throws CircleException
     *
     * @author xbantcl
     * @date   2021/3/1 9:32
     */
    public function add($uid, $content, $images = ''): bool
    {
        try {
            Circle::create([
                'uid' => 1,
                'content' => $content,
                'images' => $images
            ]);
        } catch (Exception $e) {
            throw new CircleException('ADD_CIRCLE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 获取圈子动态数据列表
     *
     * @param int $start
     * @param int $limit
     *
     * @return array
     *
     * @author xbantcl
     * @date   2021/3/2 15:32
     */
    public function getList($start, $limit = 10): array
    {
        $query = Circle::leftjoin('user as u', 'u.id', '=', 'circle.uid')
            ->select('u.id', 'u.username', 'u.avatar', 'circle.id', 'circle.content', 'circle.images', 'circle.created_at')
            ->orderBy('circle.id', 'DESC');
        if ($start > 0) {
            $query->where('circle.id', '<', $start);
        }
        $data = $query->take($limit + 1)->get()->toArray();
        $more = 0;
        if (empty($data)) {
            return ['start' => $start, 'more' => $more, 'list' => (object)[]];
        }
        if (count($data) > $limit) {
            $more = 1;
            array_pop($data);
        }
        $data = array_map(function ($item) {
            $item['content'] = [
                'text' => $item['content'],
                'images' => explode('|', $item['images']),
                'isLike' => 0,
                'like'   => [
                    [
                        'uid' => 1,
                        'username' => '小王子'
                    ],
                    [
                        'uid' => 2,
                        'username' => '小一一'
                    ]
                ],
                'comments' => [
                    'total' => 2,
                    'comment' => [
                        [
                            'uid' => 2,
                            'username' => '小海',
                            'content' => '很棒很棒'
                        ],
                        [
                            'uid' => 2,
                            'username' => '小高',
                            'content' => '很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒很棒!'
                        ]
                    ]
                ],
                'timestamp' => '1小时前'
            ];
            unset($item['images']);
        }, $data);
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }
}