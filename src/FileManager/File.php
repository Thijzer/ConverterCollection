<?php

namespace FileManager;

class File extends \SPLFileInfo
{
    /*
     * @var string
     */
    private $fullPath;
    private $content;

    /**
     * Construct
     *
     * @param string $fullPath the destination Path of a file
     * @param string $content  setup the File content
     */
    public function __construct($fullPath, $content = null)
    {
        parent::__construct($fullPath);
        $this->fullPath = $fullPath;
        $this->content = $content;
    }

    /**
     * Returns the File's Full Path
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * Return the CRC32b hash created of the full path
     * easier for checksums or matches
     *
     * @return string CRC32b hash
     */
    public function getFullPathHash()
    {
        return hash('crc32b', $this->fullPath);
    }

    public function getFilename()
    {
        return rtrim($this->getBasename($this->getExtension()), '.');
    }

    /**
     * Get File Content
     *
     * @return string file content
     */
    public function getContent()
    {
        return (!$this->content) ? @file_get_contents($this->fullPath) : $this->content;
    }

    /**
     * Set File Content
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Returns the md5 hash of the File's content
     *
     * @return string hash of file content
     */
    public function getHash()
    {
        return md5($this->getContent());
    }

    /**
     * Returns the mime Type of the File
     *
     * @return [type] [description]
     */
    public function getMimeType()
    {
        if (!$this->isfile()) {
            throw new \Exception("Error Processing Request : File content doesn't exist");
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $this->fullPath);
        finfo_close($finfo);
        return $mimetype;
    }

    /**
     * Returns the byte size of the file
     *
     * @return string
     */
    public function getSizeInBytes()
    {
        if (!$this->isfile()) {
            throw new \Exception("Error Processing Request : File content doesn't exist");
        }
        clearstatcache(); # required
        $bytesize = filesize($this->fullPath);
        return $bytesize;
    }

    /**
     * Returns the Directory the file is stored in
     *
     * @return string
     */
    public function getDirectory()
    {
        return pathinfo($this->fullPath, PATHINFO_DIRNAME);
    }

    /**
     * Returns the Formatted File size of the File
     *
     * @param  integer $decimals the number of decimals you wish to return
     *
     * @return string  Formatted Filesize
     */
    public function getFilesize($decimals = 2)
    {
        $bytes = $this->getSizeInBytes();
        $size = array("Bytes", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
        $factor = floor((strlen($bytes) - 1) / 3);
        $formattedFilesize = sprintf("%.{$decimals}f", $bytes / pow(1024, $factor));
        $formattedFilesize .= ' ' . @$size[$factor];
        return $formattedFilesize;
    }

    /** Delete */
    public function delete()
    {
        $this->content = null;
        // we still need to remove the file
        unlink($this->fullPath);
    }

    /**
     * Save Content
     *
     * @throws Exception throw if the file has no Content
     */
    public function save()
    {
        try {
            $localFile = fopen($this->fullPath, 'w+');
            fwrite($localFile, $this->getContent());
            fclose($localFile);
        } catch (\Exception $e) {
            throw new \Exception("Error Saving File " . $this->fullPath, $e);
        }
    }
}
