<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;
use Mindy\Template\Expression;

/**
 * Class FunctionCallExpression
 * @package Mindy\Template
 */
class FunctionCallExpression extends Expression
{
    protected $node;
    protected $args;

    public function __construct($node, $args, $line)
    {
        parent::__construct($line);
        $this->node = $node;
        $this->args = $args;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('$this->helper(');
        $this->node->repr($compiler);
        foreach ($this->args as $arg) {
            $compiler->raw(', ');
            $arg->compile($compiler);
        }
        $compiler->raw(')');
    }

}

