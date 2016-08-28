<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

/**
 * Class XorExpression
 * @package Mindy\Template
 */
class XorExpression extends BinaryExpression
{
    public function operator()
    {
        return 'xor';
    }
}

