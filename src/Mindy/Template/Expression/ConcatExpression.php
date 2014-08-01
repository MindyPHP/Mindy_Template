<?php

namespace Mindy\Template\Expression;

class ConcatExpression extends BinaryExpression
{
    public function operator()
    {
        return '.';
    }
}

