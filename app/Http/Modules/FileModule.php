<?php

namespace Dolphin\Ting\Http\Modules;

use Psr\Container\ContainerInterface as Container;
use Qiniu\Storage\BucketManager;

class FileModule extends Module
{
    private $accessKey;
    private $secretKey;

    protected $imageBucket;

    public function __construct(Container $container)
    {
        parent::__construct($container);

        $this->accessKey = $container->get('Config')['qiniu']['accessKey'];
        $this->secretKey = $container->get('Config')['qiniu']['secretKey'];
        $this->imageBucket = 'wendushequ-circle';
    }

    /**
     * 获取上传文件token
     *
     * @return string
     *
     * @author xbantcl
     * @date   2021/2/22 9:32
     */
    public function getUploadToken()
    {
        $auth = new \Qiniu\Auth($this->accessKey, $this->secretKey);
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
        return $auth->uploadToken($this->imageBucket);
    }

    /**
     * 删除七牛空间数据
     *
     * @param string $key 文件名称
     *
     * @return boolean
     */
    public function deleteImage($key)
    {
        $auth = new \Qiniu\Auth($this->accessKey, $this->secretKey);
        $bucket = new BucketManager($auth);
        return $bucket->delete($this->imageBucket, $key);
    }
}