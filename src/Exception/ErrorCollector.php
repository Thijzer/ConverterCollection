<?php

namespace Exception;

class ErrorCollector
{
    private $errors = array();

    /**
     * Error or notice collector
     *
     * @param  string $message
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function getErrors()
    {
        return (array) $this->errors;
    }
}
