<?php

namespace Commands\transUpDown;

use Commands\transUpDown\Component\Model\Download;
use Commands\transUpDown\Component\Model\Transmission;
use Console\CommandRequest;

class TransUpDownCommand
{
    /** @var CommandRequest */
    private $request;

    public function __construct(CommandRequest $request)
    {
        $this->request = $request;
    }

    public function getName()
    {
        return 'trans-up-down';
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
            $name = implode(' | ', array(
                $download->getId(),
                $download->getName(),
                $download->getDone(),
                $download->getStatus()
            ));

            $this->request->printLine($name);
        }
    }
}
