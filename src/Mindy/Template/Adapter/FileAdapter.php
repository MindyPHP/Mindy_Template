<?php

namespace Mindy\Template\Adapter;

use Mindy\Template\Adapter;
use RuntimeException;

class FileAdapter implements Adapter
{
    protected $source;

    public function __construct($source)
    {
        if(!is_array($source)) {
            $path = realpath($source);
            if(!$path) {
                throw new RuntimeException(sprintf('source directory %s not found', $source));
            }
            $paths = array($path);
        } else {
            $paths = array();
            foreach($source as $path) {
                if($absPath = realpath($path)) {
                    $paths[] = $absPath;
                } else {
                    throw new RuntimeException(sprintf('source directory %s not found', $path));
                }
            }
        }
        $this->source = $paths;
    }

    public function isReadable($path)
    {
        foreach($this->source as $source) {
            if(is_readable($source. '/' . $path)) {
                return true;
            }
        }

        return false;
    }

    public function lastModified($path)
    {
        foreach($this->source as $source) {
            if(is_file($source . '/' . $path)) {
                return filemtime($source . '/' . $path);
            }
        }
        return null;
    }

    public function getContents($path)
    {
        foreach($this->source as $source) {
            if(is_file($source . '/' . $path)) {
                return file_get_contents($source . '/' . $path);
            }
        }
        return null;
    }
}

