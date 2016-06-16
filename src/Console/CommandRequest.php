<?php

namespace Console;

use Exception\ErrorCollector;
use Exception\Logger;

class CommandRequest
{
    /** @var ErrorCollector  */
    private $errors;
    /** @var Argument */
    private $arguments;
    /** @var Logger */
    private $logs;
    /** @var string */
    private $command;

    public function __construct(Argument $arguments, ErrorCollector $errors, Logger $logs)
    {
        $this->errors = $errors;
        $this->logs = $logs;
        $this->arguments = $arguments;
    }

    public function errors()
    {
        return $this->errors;
    }

    public function logs()
    {
        return $this->logs;
    }

    public function displayLogs()
    {
        foreach ($this->logs()->getLogs() as $logEntry) {
            echo (string) $logEntry . PHP_EOL;
        }
    }

    public function printLine($message)
    {
        echo (string) $message .  PHP_EOL;
    }

    public function displayErrors()
    {
        foreach ($this->errors->getErrors() as $error) {
            echo (string) $error .  PHP_EOL;
        }
    }

    public function displayMessages()
    {
        if (!count($this->errors()->getErrors()) && !count($this->logs()->getLogs())) {
            $this->errors()->addError('nothing to do');
        }
        $this->displayErrors();
        $this->displayLogs();
    }

    public function hasCommand($command)
    {
        return in_array($command, array_values($this->arguments->getArgs()));
    }

    public function getNextArgumentAfter($command)
    {
        $arguments = $this->arguments->buildFromArgument($command);
        return $arguments->first();
    }
}
