<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

/**
 * Class DivExpression
 * @package Mindy\Template
 */
class DivExpression extends BinaryExpression
{
    public function operator()
    {
        return '/';
    }
}

