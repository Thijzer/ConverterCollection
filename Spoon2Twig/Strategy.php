<?php

/**
 * Created by PhpStorm.
 * User: thijzer
 * Date: 22.04.16
 * Time: 21:46
 */
Interface Strategy
{
    public function start(File $file, Converter $converter);
}
