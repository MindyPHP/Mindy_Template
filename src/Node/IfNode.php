<?php

declare(strict_types = 1);

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class IfNode
 * @package Mindy\Template
 */
class IfNode extends Node
{
    protected $tests;
    protected $else;

    public function __construct($tests, $else, $line)
    {
        parent::__construct($line);
        $this->tests = $tests;
        $this->else = $else;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $idx = 0;
        foreach ($this->tests as $test) {
            $compiler->raw(($idx++ ? "} else" : '') . 'if (', $indent);
            $test[0]->compile($compiler);
            $compiler->raw(") {\n");
            $test[1]->compile($compiler, $indent + 1);
        }
        if (!is_null($this->else)) {
            $compiler->raw("} else {\n", $indent);
            $this->else->compile($compiler, $indent + 1);
        }
        $compiler->raw("}\n", $indent);
    }
}

