<?php

declare(strict_types = 1);

namespace Mindy\Template\Expression;

/**
 * Class JoinExpression
 * @package Mindy\Template
 */
class JoinExpression extends BinaryExpression
{
    public function operator()
    {
        return ".' '.";
    }
}

