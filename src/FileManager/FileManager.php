<?php

namespace FileManager;

use Event\Listener;

class FileManager
{
    private static $excludes = array('.', '..', '.DS_Store', '@eaDir');
    private $listener;
    private $root;

    public function __construct(Listener $listener, $root = '/web/')
    {
        $this->root = __DIR__ .$root;
        $this->listener = $listener;
    }

    public function get($directory, $filename)
    {
        $file = new File($directory . $filename);
        if ($file->isFile()) {
            $this->addFile($file);
            return $file;
        }
        # not found
    }

    public function findFiles($directory, $qouta = null)
    {
        $foundFiles = array();
        foreach ($this->scan($directory) as $filename) {
            if (null !== $qouta && strpos($filename, $qouta) !== false) {
                $foundFiles[] = new File($directory.DIRECTORY_SEPARATOR.$filename);
            }
        }
        return $foundFiles;
    }

    public function scanDirectory($dir, $i = 1, $foundDirs = array())
    {
        $directories = explode('*', $dir, $i+1);
        if ($i <= count($directories)) {
            $i++;
            foreach ($directories as $directory) {
                foreach ($this->scan($directory, true) as $sub) {
                    $dir = (array) $this->scanDirectory(implode($sub, $directories), $i, $foundDirs);
                    $foundDirs = array_merge($foundDirs, $dir);
                }
            }
            return array_unique($foundDirs);
        }

        return $dir;
    }

    public function scan($directory, $withDirectories = false)
    {
        if (!is_dir($directory)) {
            return array();
        }

        return array_diff(array_filter(scandir($directory), function ($item) use ($directory, $withDirectories) {
            return $withDirectories === is_dir($directory.DIRECTORY_SEPARATOR.$item);
        }), static::$excludes);
    }

    public function copy(File $originalFile, $fileName = null)
    {
        if (null === $fileName) {
            $fileName = $originalFile->getFilename().'-copy'.$originalFile->getExtension();
        }

        return new File($originalFile->getDirectory().'/'.$fileName, $originalFile->getContent());
    }

    public function write(File $newFile, $isForced = false)
    {
        if ($isForced || !$newFile->isFile()) {
            $newFile->save();
            $this->listener->triggerEvent('newFileCreated', $newFile);
            return;
        }
        $this->listener->triggerEvent('FileIsWriteProtected', $newFile);
    }
}
