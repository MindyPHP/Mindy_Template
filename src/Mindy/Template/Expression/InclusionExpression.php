<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

class InclusionExpression extends LogicalExpression
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('(is_array(', $indent);
        $this->right->compile($compiler);
        $compiler->raw(') ? ');

            $compiler->raw('(in_array(', $indent);
            $this->left->compile($compiler);
            $compiler->raw(', (array)');
            $this->right->compile($compiler);
            $compiler->raw('))');

        $compiler->raw(' : ', $indent);

            $compiler->raw('(mb_strpos(', $indent);
            $this->right->compile($compiler);
            $compiler->raw(', ');
            $this->left->compile($compiler);
            $compiler->raw(') === 0)');

        $compiler->raw(')');
    }
}

