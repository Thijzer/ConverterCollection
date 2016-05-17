<?php

namespace Commands\transUpDown\Component\Converter\Matcher;

// needs to extends a matcher
use Commands\transUpDown\Component\Model\DownloadStatus;

class TransmissionMatcher
{
    public static function match(array $haystack)
    {
        $result = array();
        $result['id'] = trim(str_replace('Id: ', '', $haystack[1]));
        $result['name'] = trim(str_replace('Name: ', '', $haystack[2]));
        $result['location'] = trim(str_replace('Location: ', '', $haystack[8]));
        $result['hash'] = trim(str_replace('Hash: ', '', $haystack[3]));
        $result['done'] = trim(str_replace(['Percent Done: ', '%'], '', $haystack[9]));
        $result['status'] = new DownloadStatus(trim(strtolower(str_replace('State: ', '', $haystack[7]))));
        $result['size'] = strtolower(str_replace('Total size: ', '', $haystack[15]));

        return $result;
    }
}
