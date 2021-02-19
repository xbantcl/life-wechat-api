<?php

namespace Dolphin\Ting\Bootstrap\Component;

use DI\Container;
use Dolphin\Ting\Http\Validation\Validator;

/**
 * Doctrine ORM
 * @package Dolphin\Ting\Bootstrap\Component
 * @author  https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/index.html
 */
class ValidatorManager implements ComponentInterface
{
    /**
     * ValidatorManager register.
     *
     * @param Container $container
     */
    public static function register (Container $container)
    {
        $container->set('validation', function () use () {
            return new Validator();
        });
    }
}