<?php

class Spoon2TwigCommands
{
    /** @var CommandRequest  */
    private $commandRequest;
    /** @var FileManager  */
    private $fileManager;

    public function __construct(CommandRequest $command, FileManager $fileManager, Converter $converter, SpoonAdapter $spoonAdapter)
    {
        $this->commandRequest = $command;
        $this->fileManager = $fileManager;
        $this->spoonAdapter = $spoonAdapter;
        $this->converter = $converter;

        $this->start();
        $this->commandRequest->displayMessages();
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

spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// DI
$commandRequest = New CommandRequest($argv, new ErrorCollector(), new Logger());
$subscriber = new Subscriber(new TimeTracker(), $commandRequest);
$listener = new Listener($subscriber);
$fileManager = new FileManager($listener);
$converter = new Converter(new SpoonRecipe() ,$listener);


// START COMMAND
New Spoon2TwigCommand($commandRequest, $fileManager, $converter,  new SpoonAdapter());
