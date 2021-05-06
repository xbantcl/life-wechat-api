<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\CircleException;

use Dolphin\Ting\Http\Model\CircleComment;
use Dolphin\Ting\Http\Model\CirclePost;
use Dolphin\Ting\Http\Utils\Help;
use Exception;
use Psr\Container\ContainerInterface as Container;

class CircleModule extends Module
{
    protected $redis;

    public function __construct(Container $container)
    {
        $this->redis = $container->get('Cache');
    }

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
                'uid' => $uid,
                'content' => $content,
                'images' => $images
            ]);
        } catch (Exception $e) {
            throw new CircleException('ADD_CIRCLE_DATA_ERROR');
        }
        return true;
    }

    /**
     * 删除圈子内容
     *
     * @param $uid
     * @param $postId
     * @return bool
     * @throws CircleException
     */
    public function delete($uid, $postId)
    {
        try {
            CirclePost::where('id', $postId)->where('uid', $uid)->delete();
        } catch (\Exception $e) {
            throw new CircleException('DELETE_CIRCLE_COMMENT_ERROR');
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
        $query = CirclePost::leftjoin('user as u', 'u.id', '=', 'circle_posts.uid')
            ->select('u.id', 'u.username', 'u.avatar', 'circle_posts.id as post_id', 'circle_posts.content', 'circle_posts.images', 'circle_posts.created_at')
            ->orderBy('circle_posts.id', 'DESC');
        if ($start > 0) {
            if ($isPullDown) {
                $query->where('circle_posts.id', '>', $start);
            } else {
                $query->where('circle_posts.id', '<', $start);
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
        if ($isPullDown) {
            $start = current($data)['post_id'];
        } else {
            $start = end($data)['post_id'];
        }
        // 查询动态评论
        $postIds = array_map(function ($item) {
            return $item['post_id'];
        }, $data);
        $comments = CircleComment::leftjoin('user as u', 'u.id', '=', 'circle_comments.uid')
            ->leftjoin('user as u1', 'u1.id', '=', 'circle_comments.reply_uid')
            ->select('circle_comments.uid', 'u.username', 'u1.username as reply_username', 'circle_comments.content', 'circle_comments.post_id')
            ->whereIn('circle_comments.post_id', $postIds)
            ->get()->toArray();
        $tmpComments = [];
        foreach ($comments as $comment) {
            $postId = $comment['post_id'];
            if (is_null($comment['reply_username'])) {
                $comment['reply_username'] = '';
            }
            unset($comment['post_id']);
            if (!isset($tmpComments[$postId])) {
                $tmpComments[$postId] = [
                    'total' => 1,
                    'comment' => [$comment]
                ];
            } else {
                $tmpComments[$postId]['total'] ++;
                array_push($tmpComments[$postId]['comment'], $comment);
            }
        }
        $data = array_map(function ($item) use ($tmpComments) {
            if (empty($item['images'])) {
                $images = [];
            } else {
                $images = explode('|', $item['images']);
            }
            $item['content'] = [
                    'text' => $item['content'],
                    'images' => $images
            ];
            $item['islike'] = 0;
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
            if (isset($tmpComments[$item['post_id']])) {
                $item['comments'] = $tmpComments[$item['post_id']];
            } else {
                $item['comments'] = [];
            }
            $item['timestamp'] = '1小时前';
            unset($item['images']);
            return $item;
        }, $data);
        return ['start' => $start, 'more' => $more, 'list' => $data];
    }

    /**
     * 获取用户圈子列表
     *
     * @param $uid
     * @param $start
     * @param $limit
     * @param bool $isAdmin
     * @return array
     */
    public function getListByUid($uid, $start, $limit, $isAdmin = false)
    {
        if ($uid === 1) {
            $isAdmin = true;
        }
        $query = CirclePost::select('id', 'content', 'images', 'created_at')
            ->orderBy('id', 'DESC');
        if (!$isAdmin) {
            $query->where('uid', '=', $uid);
        }
        if ($start > 0) {
            $query->where('id', '<', $start);
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
        $start = end($data)['id'];
        foreach ($data as $index => &$item) {
            $item['images'] = explode('|', $item['images']);
            $item['created_at'] = date('Y-m-d', strtotime($item['created_at']));
        }
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

    /**
     * @param $uid
     * @param $commentId
     *
     * @return boolean
     *
     * @throws CircleException
     */
    public function deleteComment($uid, $commentId)
    {
        try {
            CircleComment::where('uid', '=', $uid)->where('id', '=', $commentId)->delete();
        } catch (\Exception $e) {
            throw new CircleException('DELETE_CIRCLE_COMMENT_ERROR');
        }
        return true;
    }

    /**
     * 点赞
     *
     * @param $uid
     * @param $postId
     * @param $username
     * @return bool
     */
    public function like($uid, $postId, $username)
    {
        $this->redis->HMSET('circle#' . $postId, $uid, $username);
        return true;
    }

    /**
     * 取消点赞
     * @param $uid
     * @param $postId
     * @return bool
     */
    public function unlike($uid, $postId)
    {
        $this->redis->HDEL('circle#' . $postId, $uid);
        return true;
    }
}