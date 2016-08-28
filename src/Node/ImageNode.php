<?php

declare(strict_types = 1);

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node;

/**
 * Class ImageNode
 * @package Mindy\Template
 */
class ImageNode extends Node
{
    protected $imageUrl;
    protected $params;
    protected $name;

    public function __construct($line, $imageUrl, $params, $name)
    {
        parent::__construct($line);
        $this->imageUrl = $imageUrl;
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
            $this->compileImage($compiler, $indent);
            $compiler->raw(');' . "\n");
        } else {
            $compiler->raw("echo ", $indent);
            $this->compileImage($compiler, $indent);
        }

        $compiler->raw(";\n");
    }

    protected function compileImage(Compiler $compiler, $indent = 0)
    {
        $compiler->raw('(new \Modules\Core\Helpers\ImageHelper)->process(');
        $this->imageUrl->compile($compiler);
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
