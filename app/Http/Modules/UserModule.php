<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\DBException;
use Dolphin\Ting\Http\Exception\UserException;
use Dolphin\Ting\Http\Model\User;
use Exception;
use Psr\Container\ContainerInterface as Container;

class UserModule
{
    /**
     * @param  UserIdRequest $request
     *
     * @throws UserException
     *
     * @return UserResponse
     *
     * @author wanghaibing
     * @date   2020/8/21 9:32
     */
    public function getUserById (UserIdRequest $request)
    {
        $userId = $request->getUserId();
        $user   = $this->userModel->getUserById($userId);
        // 不存在
        if (empty($user)) {
            throw new UserException('USERNAME_NON_EXIST');
        }


        return $userResponse;
    }

    /**
     * 用户登录
     *
     * @param  UserRequest $userRequest
     *
     * @throws UserException
     * @throws DBException
     * @throws Exception
     *
     * @author wanghaibing
     * @date   2020/10/13 12:10
     */
    public function signIn ()
    {
        $username = $userRequest->getUsername();
        $password = $userRequest->getPassword();
        // 这里为了演示，密码直接使用了明文，请不要直接在实际项目中使用
        try {
            $user = $this->userModel->getUserByUsernameAndPassword($username, $password);
        } catch (NoResultException $e) {
            exit($e->getMessage());
            throw new UserException('USERNAME_NON_EXIST_OR_PASSWORD_ERROR');
        } catch (NonUniqueResultException $e) {
            throw new DBException($e);
        }
        // UserId
        $userId = $user->getId();
        // 开启事务
        $this->userModel->beginTransaction();

        try {
            // 更新最后登录时间
            $user->setLastSignInTime(date('Y-m-d H:i:s'));

            $this->userModel->save($user);
            // 添加登录记录
            $userSignIn = new UserSignIn();
            $userSignIn->setUserId($userId);
            $userSignIn->setIpAddress('127.0.0.1');
            $userSignIn->setSignInTime(date('Y-m-d H:i:s'));

            $this->userSignInModel->save($userSignIn);
            // 提交事务
            $this->userModel->commit();
        } catch (Exception $e) {
            // 回滚事务
            $this->userModel->rollback();
        }
        // 发送 MQ 消息
        $message = [
            'user_id' => $userId
        ];

        $this->queue->connection(QueueConstant::VIRTUAL_HOST_DEFAULT);
        $this->queue->send(json_encode($message), QueueConstant::EXCHANGE_DEFAULT);
    }

    public function test() {
        $user = User::select()->get()->toArray();
        return $user;
    }
}