<?php

namespace Event;

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
