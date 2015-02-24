<?php

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class CsrfTokenNode
 * @package Mindy\Template
 */
class CsrfTokenNode extends Node
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw("echo '<input type=\"hidden\" value=\'' . \\Mindy\\Base\\Mindy::app()->request->csrf->getValue() . '\' name=\"' . \\Mindy\\Base\\Mindy::app()->request->csrf->getName() . '\" />';\n", $indent);
    }
}
