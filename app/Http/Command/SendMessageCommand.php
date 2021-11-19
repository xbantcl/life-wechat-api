<?php
namespace Dolphin\Ting\Http\Command;


use Dolphin\Ting\Http\Modules\CacheModule;
use Psr\Container\ContainerInterface as Container;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendMessageCommand extends Command
{
    protected $container;
    protected static $defaultName = 'send-message-notice';

    public function __construct(Container $container, string $name = null)
    {
        parent::__construct($name);
        $this->container = $container;
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->sendMessage();
    }
    public function sendMessage() {
        $accessToken = CacheModule::getInstance($this->container)->getAccessToken();
    }
}