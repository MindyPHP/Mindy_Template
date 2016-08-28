<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class PosExpression
 * @package Mindy\Template
 */
class PosExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('+');
    }
}

