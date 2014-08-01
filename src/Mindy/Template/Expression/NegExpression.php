<?php

namespace Mindy\Template\Expression;

class NegExpression extends UnaryExpression
{
    public function operator($compiler)
    {
        $compiler->raw('-');
    }
}

