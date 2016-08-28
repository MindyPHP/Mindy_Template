<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

use Mindy\Template\Compiler;

/**
 * Class NegExpression
 * @package Mindy\Template
 */
class NegExpression extends UnaryExpression
{
    public function operator(Compiler $compiler)
    {
        $compiler->raw('-');
    }
}

