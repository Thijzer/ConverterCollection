<?php

namespace Commands\Spoon2Twig\Command;

use Commands\Spoon2Twig\Helpers\SpoonAdapter;
use Commands\Spoon2Twig\Helpers\SpoonRecipe;
use Console\CommandRequest;
use Console\Init;
use Converter\Converter;
use FileManager\File;
use FileManager\FileManager;

class Spoon2TwigCommand
{
    const NAME = 'spoon2twig';

    /** @var CommandRequest  */
    private $request;
    /** @var FileManager  */
    private $fileManager;
    /** @var SpoonAdapter */
    private $spoonAdapter;
    /** @var Init */
    private $init;
    /** @var boolean */
    private $isForced;

    public function __construct(CommandRequest $request, Init $init)
    {
        $this->fileManager = new FileManager($init->getListener());
        $this->spoonAdapter = new SpoonAdapter();
        $this->request = $request;
        $this->init = $init;
    }

    public function getInformation()
    {
        $this->request->printLine(
            'usage: --file --all --module --theme'
        );
    }

    public function start()
    {
        $request = $this->request;
        // GENERAL COMMANDS
        $this->isForced = $request->hasCommand('--force', 2);

        /** force converts a given file */
        $inputFile = $request->grabArgument(2);

        switch (true) {
            case $request->hasCommand('--file', 1):
                $this->convertFileCommand($inputFile);
                break;
            case $request->hasCommand('--all'):
                $sourceDir = '';
                if ($request->hasCommand('--source', 2)) {
                    $sourceDir = $request->grabArgument(3);
                }
                $this->convertAllFilesCommand($sourceDir);
                break;
            case $request->hasCommand('--module', 1):
                $this->convertModuleCommand($inputFile);
                break;
            case $request->hasCommand('--theme', 1):
                $this->convertThemeCommand($inputFile);
                break;
            default:
                $this->getInformation();
        }
    }

    private function convertAllFilesCommand($sourceDir)
    {
        $dirs = array();
        $paths = $this->spoonAdapter->getAllSpoonBasePaths($sourceDir);
        foreach ($paths as $path) {
            $dirs = array_merge($dirs, $this->fileManager->scanDirectory($path));
        }

        $this->fileConverter($dirs);
    }
    
    private function convertFileCommand($inputFile)
    {
        if (!file_exists($inputFile)) {
            $this->request->errors()->addError('no file found for '.$inputFile);
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
            $this->request->errors()->addError('unknown module name '.$inputFile);
            return;
        }

        $this->fileConverter($this->spoonAdapter->getModulePaths());
    }

    private function convertThemeCommand($inputFile)
    {
        $themeDirectory = $this->spoonAdapter->getFrontendThemeDirectory($inputFile);

        if (!is_dir($themeDirectory[0])) {
            $this->request->errors()->addError('unknown theme name '.$inputFile);
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
            $converter = new Converter(new SpoonRecipe(), $this->init->getListener());

            /** @var File $file */
            foreach ($foundFiles as $file) {
                $this->fileManager->write(
                    $converter->parse($this->fileManager->copy($file, $file->getFilename().'.twig.html')),
                    $this->isForced
                );
            }
            if ($converter->hasExcludedFiles()) {
                $this->request->logs()->addLog('not all files are converted, use "--force" to overwrite');
            }
        }
    }
}
