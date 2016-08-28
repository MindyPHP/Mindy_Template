<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;
use Mindy\Template\Expression;

/**
 * Class ArrayExpression
 * @package Mindy\Template
 */
class ArrayExpression extends Expression
{
    protected $elements;

    public function __construct($elements, $line)
    {
        parent::__construct($line);
        $this->elements = $elements;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('array(', $indent);
        foreach ($this->elements as $node) {
            if (is_array($node)) {
                $node[0]->compile($compiler);
                $compiler->raw(' => ');
                $node[1]->compile($compiler);
            } else {
                $node->compile($compiler);
            }
            $compiler->raw(',');
        }
        $compiler->raw(')');
    }
}

