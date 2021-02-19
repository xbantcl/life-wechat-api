<?php

namespace Dolphin\Ting\Bootstrap\Component;

use DI\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Container\Container as DbContainer;
use Illuminate\Events\Dispatcher;

/**
 * Doctrine ORM
 * @package Dolphin\Ting\Bootstrap\Component
 * @author  https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/index.html
 */
class EloquentManager implements ComponentInterface
{
    /**
     * EloquentManager register.
     *
     * @param Container $container
     */
    public static function register (Container $container)
    {
        $capsule = new Capsule;
        $capsule->addConnection($container->get('Config')['database']);
        $capsule->setEventDispatcher(new Dispatcher(new DbContainer));
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }
}