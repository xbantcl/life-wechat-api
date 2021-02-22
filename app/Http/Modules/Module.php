<?php namespace olphin\Ting\Http\Modules;

use Psr\Container\ContainerInterface as Container;

/**
 * Module class.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2021-02-22
 */

class Module
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @var BaseModule
     */
    public static $instance = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * 获取modules实例.
     *
     * @return BaseModule
     */
    public static function getInstance()
    {
        $className = get_called_class();
        if (!isset(static::$instance[$className])) {
            static::$instance[$className] = new $className;
        }
        return static::$instance[$className];
    }
}