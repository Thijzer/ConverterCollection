<?php

/**
 * This file is part of Fork CMS.
 *
 * For the full copyright and license information, please view the license
 * file that was distributed with this source code.
 *
 * The Spoon2Twig Convert is a command line file converter
 * to rebuild your old templates to new twig compatible templates
 *
 * @author <thijs@wijs.be>
 */

class Converter
{
    private $interationNr = 0;
    private $strategy;
    private $listener;
    private $excludedfiles;

    public function __construct(Strategy $strategy, Listener $listener)
    {
        $this->strategy = $strategy;
        $this->listener = $listener;
    }

    public function hasExcludedFiles()
    {
        return 0 !== count($this->excludedfiles);
    }

    /** STRING CONVERSIONS START HERE **/
    public function parse(File $file)
    {
        return $this->strategy->start($file, $this);
    }

    /**
     * preg_replace sprint_f
     * Combines 2 function into one that's more ideal for parsing
     * as it string replaces any found matches with a new given value
     *
     * @param  string $regex    the regex
     * @param  string $format   the replace value
     * @param  string $filedata file content
     *
     * @return string           if successful returns file content with replaced data
     */
    public function pregReplaceSprintf($regex, $format, $filedata, $extra = null)
    {
        preg_match_all($regex, $filedata, $match);

        if (count($match)) {
            $values = array();
            foreach ($match[1] as $value) {
                if ($extra === 'snakeCase') {
                    $value = $this->fromCamelToSnake($value);
                }
                elseif($extra === 'comma') {
                    $value = $this->comma($value);
                }
                $values[] = sprintf($format, $value);
            }
            return str_replace($match[0], $values , $filedata);
        }
        //$this->error('no match found on the ' . $regex . ' line');
    }

    /**
     * Converts a noun until it's ready
     *
     * @param  string $noun a noun
     * @return string       converted noun
     */
    public function dePluralize($noun)
    {
        $nouns = array(
            'modules' => 'module'
        );

        // shorten
        $new_plur = pathinfo($noun);
        if (isset($new_plur['extension'])) {
            $noun = $new_plur['extension'];
        }

        if (in_array($noun, array_keys($nouns))) {
            $noun = $nouns[$noun];
        } elseif (substr($noun, -2) == 'es') {
            $noun = substr($noun, 0, -2);
        } elseif (substr($noun, -1) == 's') {
            $noun = substr($noun, 0, -1);
        } else {
            $noun = '_itr_'.$this->interationNr;
            $this->interationNr++;
        }
        return $noun;
    }

    public function fromCamelToSnake($input)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    /** we need to extract this */
    public function comma($input)
    {
        return str_replace(':', ',', $input);
    }
}
