<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class InclusionExpression
 * @package Mindy\Template
 */
class InclusionExpression extends LogicalExpression
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        // if (is_array($haystack))

        $compiler->raw('(is_array(', $indent);
        $this->right->compile($compiler);
        $compiler->raw(') ? ');

        // {

        $compiler->raw('(in_array(', $indent);
        $this->left->compile($compiler);
        $compiler->raw(', (array)');
        $this->right->compile($compiler);
        $compiler->raw('))');

        // } else

        $compiler->raw(' : ', $indent);

        // {

        $compiler->raw('(mb_strstr(', $indent);
        $this->right->compile($compiler);
        $compiler->raw(', ');
        $this->left->compile($compiler);
        $compiler->raw(') != false)');

        // }

        $compiler->raw(')');
    }
}

