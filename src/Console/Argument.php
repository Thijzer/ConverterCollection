<?php

namespace Console;

class Argument
{
    /** @var array */
    private $args;

    public function __construct(array $args)
    {
        $this->args = $args;
    }

    /** @return array */
    public function getArgs()
    {
        return $this->args;
    }

    public function first()
    {
        return reset($this->args);
    }

    public function getIndex($command)
    {
        return array_search($command, $this->args);
    }

    public function buildFromArgument($command)
    {
        $index = $this->getIndex($command);
        return new self(array_slice($this->args, ++$index));
    }

    public function moveOneUp()
    {
        array_shift($this->args);
        return new self($this->args);
    }
}
