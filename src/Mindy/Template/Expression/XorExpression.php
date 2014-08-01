<?php

namespace Mindy\Template\Expression;

class XorExpression extends BinaryExpression
{
    public function operator()
    {
        return 'xor';
    }
}

