<?php

namespace Commands\TransUpDown\Component\Model;

use FileManager\File;

class Download extends File
{
    /** @var string */
    private $id;
    private $name;
    private $downloadSize;
    private $location;
    private $hash;
    private $done;
    /** @var DownloadStatus */
    private $status;

    public function __construct($id, $name, $downloadSize, $location, $hash, $done, DownloadStatus $status)
    {
        $this->id = $id;
        $this->name = $name;
        $this->downloadSize = $downloadSize;
        $this->location = $location;
        $this->hash = $hash;
        parent::__construct($location);
        $this->done = $done;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return string
     */
    public function getDone()
    {
        return $this->done;
    }

    public function isCompleted()
    {
        return $this->done === '100' && $this->getStatus()->isFinished();
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /** @return DownloadStatus */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getDownloadSize()
    {
        return $this->downloadSize;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode(' | ', array(
            $this->getId(),
            $this->getName(),
            $this->getDone(),
            $this->getStatus()
        ));
    }

    public static function fromArray(array $download)
    {
        return new self(
            $download['id'],
            $download['name'],
            $download['size'],
            $download['location'],
            $download['hash'],
            $download['done'],
            $download['status']
        );
    }
}
