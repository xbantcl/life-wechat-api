<?php

namespace Dolphin\Ting\Http\Modules;

use Dolphin\Ting\Http\Exception\UserException;

use Exception;
use Dolphin\Ting\Http\Modules\Module;
use Psr\Container\ContainerInterface as Container;

class FileModule extends Module
{
    private $accessKey;
    private $secretKey;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->accessKey = $container->get('Config')['qiniu']['accessKey'];
        $this->secretKey = $container->get('Config')['qiniu']['secretKey'];
    }

    /**
     * @param UserIdRequest $request
     *
     * @return UserResponse
     *
     * @throws UserException
     *
     * @author xbantcl
     * @date   2021/2/22 9:32
     */
    public function getUploadToken()
    {
        $qiniu = new \Qiniu\Auth($this->accessKey, $this->secretKey);
        $putPolicy = [
            'scope'      => '',
            'deadline'   => 1451491200,
            'returnBody' => [
                'name' => '$(fname)',
                'size' => '$(size)',
                'w'    => '$(imageInfo.width)',
                'h'    => '$(imageInfo.height)',
                'hash' => '$(etag)'
            ]
        ];
        return $qiniu->uploadToken('wendushequ-circle');
    }
}