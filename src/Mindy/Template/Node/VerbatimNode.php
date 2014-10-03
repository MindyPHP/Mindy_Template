<?php
/**
 * 
 *
 * All rights reserved.
 * 
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 04/08/14.08.2014 19:57
 */

namespace Mindy\Template\Node;


use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * TODO
 * Class VerbatimNode
 * @package Mindy\Template\Node
 */
class VerbatimNode extends OutputNode
{
    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
    }
}
