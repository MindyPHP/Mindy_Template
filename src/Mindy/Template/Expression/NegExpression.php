<?php

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

class NegExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('-');
    }
}

