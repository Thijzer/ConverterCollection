<?php

namespace Commands\TransUpDown\Component\Model;

use Commands\TransUpDown\Component\Converter\Matcher\TransmissionMatcher;
use Commands\TransUpDown\Component\Reader\TransmissionFileReader;

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
     * @return \Commands\TransUpDown\Component\Model\Download[]
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

    public function remove()
    {
        foreach ($this->getDownloads() as $download) {
            $this->reader->clean($download->getId());
        }
    }
}
