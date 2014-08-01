<?php

namespace Mindy\Template\Expression;

class NotExpression extends UnaryExpression
{
    public function operator($compiler)
    {
        $compiler->raw('!');
    }
}

