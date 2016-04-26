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

class ErrorCollector
{
    private $errors = array();

    /**
     * Error or notice collector
     *
     * @param  string $message
     */
    public function addError($message)
    {
        $this->errors[] = $message;
    }

    public function getErrors()
    {
        return (array) $this->errors;
    }
}
