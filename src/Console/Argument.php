<?php

namespace Console;

class Argument
{
    /** @var array */
    private $args;

    public function __construct(array $args)
    {
        array_shift($args);
        $this->args = $args;
    }

    /** @return array */
    public function getArgs()
    {
        return $this->args;
    }
}
