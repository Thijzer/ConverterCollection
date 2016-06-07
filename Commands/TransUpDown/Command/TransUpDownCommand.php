<?php

namespace Commands\TransUpDown\Command;

use Commands\TransUpDown\Component\Model\Transmission;
use Console\CommandRequest;

class TransUpDownCommand
{
    const NAME = 'trans-up-down';

    /** @var CommandRequest */
    private $request;

    public function __construct(CommandRequest $request)
    {
        echo 'f';
        $this->request = $request;
    }

    public function getInformation()
    {
        $this->request->printLine(
            'usage: -sleep -wake -clean -list'
        );
    }

    public function start()
    {
        $request = $this->request;
        switch (true) {
            case $request->hasCommand('--clean', 1):
                $this->cleanCommand();
                break;
            case $request->hasCommand('--wake', 1):
                $this->wakeCommand();
                break;
            case $request->hasCommand('--sleep', 1):
                $this->sleepCommand();
                break;
            case $request->hasCommand('--list', 1):
                $this->listCommand();
                break;
            default:
                $this->getInformation();
        }
    }

    public function sleepCommand()
    {
        $transmission = new Transmission();
        $transmission->sleep();
    }

    public function wakeCommand()
    {
        $transmission = new Transmission();
        $transmission->wakeUp();
    }

    public function cleanCommand()
    {
        $transmission = new Transmission();
        $transmission->cleanUp();
    }

    public function listCommand()
    {
        $transmission = new Transmission();
        $downloadedItems = $transmission->getDownloads();

        foreach ($downloadedItems as $download) {
            $this->request->printLine($download);
        }
    }
}
