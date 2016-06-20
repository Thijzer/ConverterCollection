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

    public function getNext()
    {
        array_shift($this->args);
        return $this->first();
    }

    public function getIndex($argument)
    {
        return array_search($argument, $this->args);
    }

    public function hasArgument($argument)
    {
        return in_array($argument, $this->args);
    }

    public function buildFromArgument($argument)
    {
        $index = $this->getIndex($argument);
        return new self(array_slice($this->args, ++$index));
    }

    public function moveOneUp()
    {
        array_shift($this->args);
        return new self($this->args);
    }
}
