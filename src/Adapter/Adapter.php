<?php

declare(strict_types = 1);

namespace Mindy\Template\Adapter;

/**
 * Interface Adapter
 * @package Mindy\Template
 */
interface Adapter
{
    /**
     * @param $path
     * @return mixed
     */
    public function isReadable($path) : bool;

    /**
     * @param $path
     * @return mixed
     */
    public function lastModified($path);

    /**
     * @param $path
     * @return mixed
     */
    public function getContents($path);
}

