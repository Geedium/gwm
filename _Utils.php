<?php

function _autoload($path, $extensions, $flags = 0)
{
    $iterations = new FilesystemIterator($fp, $flags);
    foreach ($iterations as $iteration) {
        $extension = $fp->getExtension();
        
        if (in_array($extension, $extensions) == false) {
            continue;
        }

        $basename = $fp->getBasename($extension);
        include_once($path.'/'.$fp->getFilename());
    }
}