<?php

namespace Commands\transUpDown\Component\Model;

use Commands\transUpDown\Component\Converter\Matcher\TransmissionMatcher;
use Commands\transUpDown\Component\Reader\TransmissionFileReader;

// Provider
class Transmission
{
    /** @var DownloadList */
    private $list;
    /** @var TransmissionFileReader */
    private $reader;

    public function __construct()
    {
        $this->reader = new TransmissionFileReader();

        $items = array();
        foreach ($this->reader->getList() as $item) {
            $items[] = TransmissionMatcher::match($item);
        }

        $this->list = new DownloadList();
        $this->list->setDownloadedItems($items);
    }

    /**
     * @return \Commands\transUpDown\Component\Model\Download[]
     */
    public function getDownloads()
    {
        return $this->list->getDownloadedItems();
    }

    public function wakeUp()
    {
        foreach ($this->getDownloads() as $download) {
            if (!$download->isCompleted()) {
                $this->reader->wake($download->getId());
            }
        }
    }

    public function sleep()
    {
        $this->reader->sleep();
    }

    public function cleanUp()
    {
        foreach ($this->getDownloads() as $download) {
            if ($download->isCompleted()) {
                $this->reader->clean($download->getId());
            }
        }
    }
}
