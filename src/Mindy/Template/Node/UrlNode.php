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
 * @date 01/08/14.08.2014 19:09
 */

namespace Mindy\Template\Node;


use Mindy\Template\Compiler;
use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node;

class UrlNode extends Node
{
    protected $route;
    protected $params;
    protected $name;

    public function __construct($line, $route, $params, $name)
    {
        parent::__construct($line);
        $this->route = $route;
        $this->params = $params;
        $this->name = $name;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);

        if ($this->name) {
            $name = "\$context['$this->name']";
            $compiler->raw("if (!isset($name)) $name = null;\n" . "\n", $indent);
            $compiler->raw("\$this->setAttr($name, array(), ", $indent);
            $this->compileUrl($compiler, $indent);
            $compiler->raw(');' . "\n");
        } else {
            $compiler->raw("echo ", $indent);
            $this->compileUrl($compiler, $indent);
        }

        $compiler->raw(";\n");
    }

    protected function compileUrl(Compiler $compiler, $indent)
    {
        $compiler->raw('\Mindy\Base\Mindy::app()->urlManager->reverse(');
        $this->route->compile($compiler);
        $compiler->raw(', ');

        if ($this->params instanceof ArrayExpression) {
            $this->params->compile($compiler);
        } else {
            $compiler->raw('array(');
            foreach ($this->params as $key => $expression) {
                if (is_string($key)) {
                    $compiler->raw("'$key'", 1);
                } else {
                    $expression->compile($compiler, 1);
                }
                $compiler->raw(',');
            }
            $compiler->raw(')');
        }
        $compiler->raw(')');
    }
}
