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
        $this->commandRequest = new CommandRequest($arg, new ErrorCollector(), new Logger());
        $this->subscriber = new Subscriber(new TimeTracker(), $this->commandRequest);
        $this->listener = new Listener($this->subscriber);

        foreach ($this->getSources() as $commandName => $source) {
            if ($commandName === $arg->first()) {
                $command = new $source['FQCN']($this->commandRequest, $this);
                $command->start();
            }
        }

        if (!isset($command)) {
            $this->commandRequest->errors()
                ->addError('following command are supported : '.implode(', ', array_keys($this->getSources())));
        }

        $this->commandRequest->displayMessages();
    }

    public function getSources()
    {
        $sources = array_diff(scandir(__DIR__.'/../../Commands'), array('.', '..'));

        $foundCommands = array();
        foreach ($sources as $source) {
            $className = 'Commands\\'.$source.'\\Command\\'.ucfirst($source).'Command';
            if (class_exists($className)) {
                $class = new \ReflectionClass($className);
                if (null !== $commandName = $class->getConstant('NAME')) {
                    $foundCommands[$commandName] = array('FQCN' => $className);
                }
            }
        }

        return $foundCommands;
    }

    /**
     * @return \Event\Listener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return \Event\Subscriber
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }
}
