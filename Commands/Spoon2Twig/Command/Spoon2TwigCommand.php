<?php

namespace Commands\Spoon2Twig\Command;

use Commands\Spoon2Twig\Helpers\SpoonAdapter;
use Console\CommandRequest;
use Console\Init;
use FileManager\FileManager;

class Spoon2TwigCommand
{
    /** @var CommandRequest  */
    private $commandRequest;
    /** @var FileManager  */
    private $fileManager;
    /** @var SpoonAdapter */
    private $spoonAdapter;
    /** @var Init */
    private $init;

    public function __construct(CommandRequest $command, Init $init)
    {
        $this->fileManager = new FileManager($init->getListener());
        $this->spoonAdapter = new SpoonAdapter();
        $this->commandRequest = $command;
        $this->init = $init;
    }

    public function getName()
    {
        return 'spoon-2-twig';
    }

    public function start()
    {
        // GENERAL COMMANDS
        $this->isForced = $this->commandRequest->hasCommand('--force', 2);

        /** force converts a given file */
        $inputFile = $this->commandRequest->grabArgument(2);

        if ($this->commandRequest->hasCommand('--file', 1)) {
            $this->convertFileCommand($inputFile);
            return;
        }

        /** force converts all project files */
        if ($this->commandRequest->hasCommand('--all')) {
            $this->convertAllFilesCommand();
            return;
        }

        /** Converts all module files */
        if ($this->commandRequest->hasCommand('--module', 1)) {
            $this->convertModuleCommand($inputFile);
            return;
        }

        /** Converts all Theme files */
        if ($this->commandRequest->hasCommand('--theme', 1)) {
            $this->convertThemeCommand($inputFile);
            return;
        }
    }

    private function convertAllFilesCommand()
    {
        $dirs = array();
        $paths = $this->spoonAdapter->getAllSpoonBasePaths();
        foreach ($paths as $path) {
            $dirs = array_merge($dirs, $this->fileManager->scanDirectory($path));
        }

        $this->fileConverter($dirs);
    }
    
    private function convertFileCommand($inputFile)
    {
        if (!file_exists($inputFile)) {
            $this->commandRequest->errors()->addError('no file found for '.$inputFile);
            return;
        }

        $this->fileManager->write(
            $this->converter->ruleParser($this->fileManager->getFile($inputFile)),
            $this->isForced
        );
    }

    private function convertModuleCommand($inputFile)
    {
        $moduleDirectory = $this->spoonAdapter->getModuleDirectory($inputFile);

        if (!is_dir($moduleDirectory)) {
            $this->commandRequest->errors()->addError('unknown module name '.$inputFile);
            return;
        }

        $this->fileConverter($this->spoonAdapter->getModulePaths());
    }

    private function convertThemeCommand($inputFile)
    {
        $themeDirectory = $this->spoonAdapter->getFrontendThemeDirectory($inputFile);

        if (!is_dir($themeDirectory[0])) {
            $this->commandRequest->errors()->addError('unknown theme name '.$inputFile);
            return;
        }

        $this->fileConverter($this->spoonAdapter->getThemePaths());
    }

    private function fileConverter(array $directories)
    {
        $foundFiles = array();
        foreach ($directories as $directory) {
            $foundFiles = array_merge($foundFiles, $this->fileManager->findFiles($directory, '.tpl'));
        }

        if (count($foundFiles)) {
            foreach ($foundFiles as $file) {
                $this->fileManager->write(
                    $this->converter->parse($this->fileManager->copy($file, $file->getFilename().'.twig.html')),
                    $this->isForced
                );
                exit;
            }
            if ($this->converter->hasExcludedFiles()) {
                $this->commandRequest->logs()->addLog('not all files are converted, use "--force" to overwrite');
            }
        }
    }
}
