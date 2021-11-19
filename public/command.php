#!/usr/bin/env php
<?php
// Autoload
require __DIR__ . '/../vendor/autoload.php';
// Bootstrap
$container = (require __DIR__ . '/../app/Bootstrap/app.php')['container'];

use Symfony\Component\Console\Application;
use Dolphin\Ting\Http\Command\GenerateRandomUserCommand;
use Dolphin\Ting\Http\Command\SendMessageCommand;

$application = new Application();
// 注册命令
$application->add(new GenerateRandomUserCommand($container));
$application->add(new SendMessageCommand($container));

try {
    $application->run();
} catch (Exception $e) {
    echo $e->getMessage();
}

