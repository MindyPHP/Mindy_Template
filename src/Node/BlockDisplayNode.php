<?php

declare(strict_types = 1);

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class BlockDisplayNode
 * @package Mindy\Template
 */
class BlockDisplayNode extends Node
{
    protected $name;

    public function __construct($name, $line)
    {
        parent::__construct($line);
        $this->name = $name;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw(
            '$this->displayBlock(\'' . $this->name .
            '\', $context, $blocks, $macros, $imports);' . "\n", $indent
        );
    }
}

