<?php
// 生成随机用户
namespace Dolphin\Ting\Http\Command;

use Dolphin\Ting\Http\Constant\UserConstant;
use Dolphin\Ting\Http\Entity\User;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Container\ContainerInterface as Container;
use DateTime;

class SendMessageCommand extends Command
{
    public function sendMessage() {
        exit('ddddd');
    }
}

$obj = new SendMessageCommand();
$obj->sendMessage();