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

class TimeTracker
{
    private $previousTimeStamp = 0;
    private $startTime;

    public function __construct()
    {
        $this->startTime = microtime(true);
    }

    /**
     * Stamps the time it takes from start to finnish
     * @param  integer $int how precise you wish to measure
     * @return int
     */
    public function timestamp($int = null)
    {
        return 1000 * (float)substr(microtime(true) - $this->startTime, 0, (int)$int+5);
    }

    public function setPreviousTime($time)
    {
        $this->previousTimeStamp = $time;
    }

    public function getPreviousTimeStamp()
    {
        return $this->previousTimeStamp;
    }
}
