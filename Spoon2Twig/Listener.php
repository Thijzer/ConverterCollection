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

class Listener
{
    private $subscriber;
    public function __construct(Subscriber $subscriber)
    {
        $this->subscriber = $subscriber;
    }

    public function triggerEvent($eventName, $context)
    {
        $this->subscriber->$eventName($context);
    }
}
