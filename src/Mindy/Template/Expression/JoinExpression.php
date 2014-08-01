<?php

namespace Mindy\Template\Expression;

class JoinExpression extends BinaryExpression
{
    public function operator()
    {
        return ".' '.";
    }
}

