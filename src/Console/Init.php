<?php

namespace Console;

use Event\Listener;
use Event\Subscriber;
use Exception\ErrorCollector;
use Exception\Logger;
use Tracking\TimeTracker;

class Init
{
    private $commandRequest;
    private $subscriber;
    private $listener;

    public function __construct(Argument $arg)
    {
        $this->commandRequest = new CommandRequest($arg->getArgs(), new ErrorCollector(), new Logger());
        $this->subscriber = new Subscriber(new TimeTracker(), $this->commandRequest);
        $this->listener = new Listener($this->subscriber);

        $this->getTransUpDown();
    }

    public function getTransUpDown()
    {
        $command = new \Commands\transUpDown\TransUpDownCommand($this->commandRequest);
        $command->start();
        $this->commandRequest->displayMessages();
    }
}
