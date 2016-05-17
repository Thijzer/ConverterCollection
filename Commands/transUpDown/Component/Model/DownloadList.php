<?php

namespace Commands\transUpDown\Component\Model;

class DownloadList
{
    /** @var \Commands\transUpDown\Component\Model\Download[] */
    private $downloadedItems = [];

    /**
     * @param \Commands\transUpDown\Component\Model\Download[] $downloadedItems
     */
    public function setDownloadedItems(array $downloadedItems)
    {
        foreach ($downloadedItems as $download) {
            $this->downloadedItems[] = Download::fromArray($download);
        }
    }

    /**
     * @return \Commands\transUpDown\Component\Model\Download[]
     */
    public function getDownloadedItems()
    {
        return $this->downloadedItems;
    }
}
