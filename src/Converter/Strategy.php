<?php

namespace Converter;

use FileManager\File;

Interface Strategy
{
    public function start(File $file, Converter $converter);
}
