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
        $this->request = $request;
    }

    public function getInformation()
    {
        $this->request->printLine(
            'usage: --sleep --wake --clean --list --remove'
        );
    }

    public function start()
    {
        $request = $this->request;
        switch (true) {
            case $request->hasCommand('--clean'):
                $this->cleanCommand();
                break;
            case $request->hasCommand('--wake'):
                $this->wakeCommand();
                break;
            case $request->hasCommand('--sleep'):
                $this->sleepCommand();
                break;
            case $request->hasCommand('--list'):
                $this->listCommand();
                break;
            case $request->hasCommand('--remove'):
                $this->removeCommand();
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

    public function removeCommand()
    {
        $transmission = new Transmission();
        $transmission->remove();
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
