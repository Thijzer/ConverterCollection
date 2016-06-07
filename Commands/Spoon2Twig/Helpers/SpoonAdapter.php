<?php

namespace Commands\spoon2twig\Helpers;

class SpoonAdapter
{
    private $options = array(
        'absolute' => 'false',
        'version' => '3.9',
    );

    public $baseFrontendTheme = array(
        'Frontend/Themes/*/Core/Layout/Templates',
        'Frontend/Themes/*/Core/Layout/Widgets',
    );

    public $baseFrontendThemeModule = array(
        'Frontend/Themes/*/Modules/*/Layout/Templates',
        'Frontend/Themes/*/Modules/*/Layout/Widgets',
    );

    public $baseFrontendModule = array(
        'Frontend/Modules/*/Layout/Templates',
        'Frontend/Modules/*/Layout/Widgets',
    );

    public $baseBackendModule = array(
        'Backend/Modules/*/Layout/Templates',
        'Backend/Modules/*/Layout/Widgets'
    );

    public $baseBackendCore = array(
        'Backend/Core/Layout/Templates',
    );

    public function __construct($options = array())
    {
        $this->options = array_merge($options, $this->options);
        if (true === $this->options['absolute']) {
            $this->source = __DIR__ . 'SpoonAdapter.php/';
        }
        $this->source .= $this->getCorrectSourceVersion($this->options['version']);
    }

    public function getAllSpoonBasePaths()
    {
        return $this->addRoot(array_merge(
            $this->baseFrontendTheme,
            $this->baseFrontendThemeModule,
            $this->baseFrontendModule,
            $this->baseBackendModule,
            $this->baseBackendCore
        ));
    }

    private function addRoot(array $paths)
    {
        foreach ($paths as &$path) {
            $path = $this->source.$path;
        }
        return $paths;
    }

    public function getFrontendThemeDirectory($themeName)
    {
        return $this->addRoot($this->replaceName($themeName, array_merge(
            $this->baseFrontendTheme,
            $this->baseFrontendThemeModule
        )));
    }

    public function getModuleDirectory($moduleName)
    {
        return $this->addRoot($this->replaceName($moduleName, array_merge(
            $this->baseFrontendThemeModule,
            $this->baseFrontendModule,
            $this->baseBackendModule
        )));
    }

    public function replaceName($name, array $content)
    {
        function str_replace_limit($needle, $replace, $haystack) {
            $pos = strpos($haystack, $needle);
            if ($pos !== false) {
                return substr_replace($haystack, $replace, $pos, strlen($needle));
            }
        }

        $a = array();
        foreach ($content as $c) {
            $a[] = str_replace_limit('*', $name, $c);
        }
        return $a;
    }

    /**
     * Get Correct version looks a the project version
     * to find and return it's source directory
     *
     * @return string returns the correct source dir
     */
    public function getCorrectSourceVersion($version)
    {
        // checking what version
        switch (true)
        {
            case (strpos($version, '3.9') !== false):
                $source = 'src/';
                break;

            case (strpos($version, '3.8') !== false):
                $source = 'src/';
                break;

            default:
                $source = 'src/';
                break;
        }

        return $source;
    }
}
