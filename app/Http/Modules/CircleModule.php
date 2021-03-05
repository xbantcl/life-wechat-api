<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;

use Dolphin\Ting\Http\Model\CircleComment;
use Dolphin\Ting\Http\Model\CirclePost;
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
            CirclePost::create([
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
     * @param int     $start      起始位置
     * @param boolean $isPullDown 下拉刷新
     * @param int     $limit      限制条数
     *
     * @return array
     *
     * @author xbantcl
     * @date   2021/3/2 15:32
     */
    public function getList($start, $isPullDown = false, $limit = 10): array
    {
        $query = CirclePost::leftjoin('user as u', 'u.id', '=', 'circle.uid')
            ->select('u.id', 'u.username', 'u.avatar', 'circle.id as post_id', 'circle.content', 'circle.images', 'circle.created_at');
        if ($start > 0) {
            if ($isPullDown) {
                $query->where('circle.id', '>', $start)->orderBy('circle.id', 'ASC');
            } else {
                $query->where('circle.id', '<', $start)->orderBy('circle.id', 'DESC');
            }
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
        $start = end($data)['post_id'];
        $data = array_map(function ($item) {
            $item['content'] = [
                    'text' => $item['content'],
                    'images' => explode('|', $item['images'])
            ];
            $item['isLike'] = 0;
            $item['like']  = [
                [
                    'uid' => 1,
                    'username' => '小王子'
                ],
                [
                    'uid' => 2,
                    'username' => '小一一'
                ]
            ];
            $item['comments'] = [
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
            ];
            $item['timestamp'] = '1小时前';
            unset($item['images']);
            return $item;
        }, $data);
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * 发布圈子动态评论
     *
     * @param int    $uid        评论作者id
     * @param int    $replyUid   被回复的用户id
     * @param int    $postId     圈子动态id
     * @param string $content    评论类容
     *
     * @return mixed
     *
     * @throws CircleException
     */
    public function comment($uid, $replyUid, $postId, $content)
    {
        try {
            $circleComment = CircleComment::create([
                'uid' => $uid,
                'reply_uid' => $replyUid,
                'post_id' => $postId,
                'content' => $content
            ]);
        } catch (\Exception $e) {
            throw new CircleException('ADD_CIRCLE_COMMENT_ERROR');
        }
        return $circleComment->id;
    }
}