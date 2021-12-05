<?php

namespace Dolphin\Ting\Http\Service;

use Dolphin\Ting\Http\Modules\CircleModule;
use Dolphin\Ting\Http\Modules\FileModule;
use Dolphin\Ting\Http\Response\ServiceResponse;
use Dolphin\Ting\Http\Utils\Help;
use Psr\Container\ContainerInterface as Container;
use Respect\Validation\Validator as v;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class CircleService extends Service
{
    private $validation;
    private $uid;

    public function __construct (Container $container)
    {
        parent::__construct($container);

        $this->validation = $container->get('validation');
        $this->uid = $container->get('uid');
    }

    /**
     * 添加圈子动态数据
     *
     * @param Request $request
     * @param Response $response
     * @return ServiceResponse
     */
    public function add(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'content' => v::optional(v::notEmpty()),
            'images'  => v::optional(v::stringVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $content = isset($params['content']) ? $params['content'] : '';
        $images = isset($params['images']) ? $params['images'] : '';
        $address = isset($params['address']) ? trim($params['address']) : '';
        $gpsAddress = isset($params['gps_address']) ? trim($params['gps_address']) : '';
        $lat = isset($params['lat']) ? trim($params['lat']) : 0;
        $lng = isset($params['lng']) ? trim($params['lng']) : 0;
        $data = CircleModule::getInstance($this->container)->add($this->uid, $content, $images, $address, $gpsAddress, $lat, $lng);
        return new ServiceResponse($data);
    }

    /**
     * 获取圈子动态数据列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function getList(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'is_pull_down' => v::in([0,1]),
            'start' => v::optional(v::intVal()),
            'limit'  => v::optional(v::intVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 10;
        $isPullDown = isset($params['is_pull_down']) ? boolval($params['is_pull_down']) : false;
        $data = CircleModule::getInstance($this->container)->getList($this->uid, $start, $isPullDown, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 获取圈子动态数据列表
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function getListByUid(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::optional(v::intVal()),
            'limit'  => v::optional(v::intVal())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $start = isset($params['start']) ? intval($params['start']) : 0;
        $limit = isset($params['limit']) ? intval($params['limit']) : 6;
        $data = CircleModule::getInstance($this->container)->getListByUid($this->uid, $start, $limit);
        return new ServiceResponse($data);
    }

    /**
     * 发布圈子动态评论
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function comment(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'reply_uid' => v::optional(v::intVal()),
            'post_id'  => v::intVal(),
            'content'  => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $replyUid = isset($params['reply_uid']) ? intval($params['reply_uid']) : 0;
        $postId = isset($params['post_id']) ? intval($params['post_id']) : 0;
        $data = CircleModule::getInstance($this->container)->comment($this->uid, $replyUid, $postId, $params['content']);
        return new ServiceResponse($data);
    }

    /**
     * 删除圈子动态
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function delete(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'post_id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $postId = isset($params['post_id']) ? intval($params['post_id']) : 0;
        $data = CircleModule::getInstance($this->container)->delete($this->uid, $postId);
        return new ServiceResponse($data);
    }

    /**
     * 删除圈子动态评论
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function deleteComment(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'comment_id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $commentId = isset($params['comment_id']) ? intval($params['comment_id']) : 0;
        $data = CircleModule::getInstance($this->container)->deleteComment($this->uid, $commentId);
        return new ServiceResponse($data);
    }

    /**
     * 点赞圈子动态
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function like(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'post_id'  => v::intVal(),
            'username' => v::notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $postId = isset($params['post_id']) ? intval($params['post_id']) : 0;
        $username = isset($params['username']) ? trim($params['username']) : '';
        $data = CircleModule::getInstance($this->container)->like($this->uid, $postId, $username);
        return new ServiceResponse($data);
    }

    /**
     * 取消点赞圈子动态
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function unlike(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'post_id'  => v::intVal()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $postId = isset($params['post_id']) ? intval($params['post_id']) : 0;
        $data = CircleModule::getInstance($this->container)->unlike($this->uid, $postId);
        return new ServiceResponse($data);
    }

    /**
     * 更改动态状态
     *
     * @param Request $request
     * @param Response $response
     *
     * @return ServiceResponse
     */
    public function changeCircleStatus(Request $request, Response $response)
    {
        $validation = $this->validation->validate($request, [
            'post_id'  => v::intVal(),
            'status' => v::in([1, 2, 3])
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $params = Help::getParams($request);
        $postId = intval($params['post_id']);
        $status = intval($params['status']);
        $data = CircleModule::getInstance($this->container)->changeCircleStatus($postId, $status);
        return new ServiceResponse($data);
    }
}