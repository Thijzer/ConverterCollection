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
        $this->isForced = $request->hasCommand('-f');

        switch (true) {
            case $request->hasCommand('--file'):
                $this->convertFileCommand($request->getNextArgumentAfter('--file'));
                break;
            case $request->hasCommand('--all'):
                $sourceDir = '';
                if ($request->hasCommand('--source')) {
                    $sourceDir = $request->getNextArgumentAfter('--source');
                }
                $this->convertAllFilesCommand($sourceDir);
                break;
            case $request->hasCommand('--module'):
                $this->convertModuleCommand($request->getNextArgumentAfter('--module'));
                break;
            case $request->hasCommand('--theme'):
                $this->convertThemeCommand($request->getNextArgumentAfter('--theme'));
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

        $converter = new Converter(new SpoonRecipe(), $this->init->getListener());

        $file = new File($inputFile);
        $this->fileManager->write(
            $converter->parse($this->fileManager->copy($file, $file->getFilename().'.twig.html')),
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
                    $converter->parse($this->fileManager->copy($file, $file->getFilename().'.html.twig')),
                    $this->isForced
                );
            }
            if ($converter->hasExcludedFiles()) {
                $this->request->logs()->addLog('not all files are converted, use "--force" to overwrite');
            }
        }
    }
}
