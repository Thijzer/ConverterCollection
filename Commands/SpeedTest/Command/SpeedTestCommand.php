<?php

namespace Commands\SpeedTest\Command;

use Console\CommandRequest;

class SpeedTestCommand
{
    const NAME = 'speed-test';

    /** @var CommandRequest */
    private $request;

    public function __construct(CommandRequest $request)
    {
        $this->request = $request;
    }

    public function start()
    {
        require(__DIR__ . '/../SpeedTest.php');
    }
}
