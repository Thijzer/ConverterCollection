<?php

namespace Exception;

class Logger
{
    private $logger = array();

    /**
     * Error or notice collector
     *
     * @param  string $message
     */
    public function addLog($message)
    {
        $this->logger[] = $message;
    }

    public function getLogs()
    {
        return $this->logger;
    }
}
