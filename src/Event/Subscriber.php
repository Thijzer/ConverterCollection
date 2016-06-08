<?php

namespace Event;

use Console\CommandRequest;
use FileManager\File;
use Tracking\TimeTracker;

class Subscriber
{
    private $tracker;
    private $command;
    
    public function __construct(TimeTracker $tracker, CommandRequest $command)
    {
        $this->tracker = $tracker;
        $this->command = $command;
    }

    public function newFileCreated(File $file)
    {
        $newTime = $this->tracker->timestamp(2) - $this->tracker->getPreviousTimeStamp();
        $this->tracker->setPreviousTime($this->tracker->timestamp(2));

        $this->command->printLine(
            $file->getFullPath(). ' done in ' . $newTime . ' milliseconds'
        );
    }

    public function fileIsWriteProtected(File $file)
    {
        $this->command->printLine(
            'twig version of ' . $file->getBasename() . ' found, use the "-f" parameter to overwrite'
        );
    }
}
