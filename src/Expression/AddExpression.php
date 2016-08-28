<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

/**
 * Class AddExpression
 * @package Mindy\Template
 */
class AddExpression extends BinaryExpression
{
    public function operator()
    {
        return '+';
    }
}

