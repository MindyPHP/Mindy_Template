<?php

declare(strict_types = 1);

namespace Mindy\Template;

/**
 * Class Node
 * @package Mindy\Template
 */
class Node
{
    /**
     * @var int
     */
    protected $line;

    /**
     * Node constructor.
     * @param $line
     */
    public function __construct(int $line)
    {
        $this->line = $line;
    }

    /**
     * @return mixed
     */
    public function getLine() : int
    {
        return $this->line;
    }

    /**
     * @param \Mindy\Template\Compiler $compiler
     * @param $indent
     */
    public function addTraceInfo(Compiler $compiler, $indent)
    {
        $compiler->addTraceInfo($this, $indent);
    }

    /**
     * @param Compiler $compiler
     * @param int $indent
     */
    public function compile(Compiler $compiler, $indent = 0)
    {
    }
}

