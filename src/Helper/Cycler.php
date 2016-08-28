<?php

declare(strict_types = 1);

namespace Mindy\Template\Helper;

use ArrayIterator;
use IteratorAggregate;

/**
 * Class Cycler
 * @package Mindy\Template
 */
class Cycler implements IteratorAggregate
{
    protected $elements;
    protected $length;
    protected $idx;

    public function __construct($elements)
    {
        $this->elements = $elements;
        $this->length = count($this->elements);
        $this->idx = 0;
    }

    public function getIterator()
    {
        return new ArrayIterator($this->elements);
    }

    public function next()
    {
        return $this->elements[($this->idx++) % ($this->length)];
    }

    public function random($seed = null)
    {
        if (isset($seed)) {
            mt_srand($seed);
        }
        return $this->elements[mt_rand(0, $this->length - 1)];
    }

    public function count()
    {
        return $this->idx;
    }

    public function cycle()
    {
        return ceil($this->idx / $this->length);
    }
}