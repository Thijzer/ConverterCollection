<?php

namespace Console;

use Exception\ErrorCollector;
use Exception\Logger;

class CommandRequest
{
    /** @var ErrorCollector  */
    private $errors;
    /** @var  array */
    private $arguments;
    /** @var Logger */
    private $logs;

    public function __construct(array $argv, ErrorCollector $errors, Logger $logs)
    {
        $this->errors = $errors;
        $this->logs = $logs;

        // OUR INPUT
        $this->arguments = $argv;

        if (false === $this->existsCommand()) {
            $this->printLine('no arguments given');
            exit;
        }
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

    public function hasCommand($command = '', $index = 1)
    {
        return ($this->existsCommand($index) && $this->arguments[$index] === $command);
    }

    public function existsCommand($index = 1)
    {
        return array_key_exists($index, $this->arguments);
    }

    public function grabArgument($index = 1)
    {
        return $this->existsCommand($index) ? (string) trim($this->arguments[$index]) : null;
    }
}
