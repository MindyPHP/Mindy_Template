<?php

namespace Mindy\Template;

class Node
{
    protected $line;

    public function __construct($line)
    {
        $this->line = $line;
    }

    public function getLine()
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

    public function compile(Compiler $compiler, $indent = 0)
    {
    }
}

