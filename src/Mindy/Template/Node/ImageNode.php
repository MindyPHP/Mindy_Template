<?php

namespace Mindy\Template\Node;

use Mindy\Template\Compiler;
use Mindy\Template\Node;

/**
 * Class ImageNode
 * @package Mindy\Template
 */
class ImageNode extends Node
{
    protected $route;
    protected $params;
    protected $name;

    public function __construct($line, $url, $prefix)
    {
        parent::__construct($line);
        $this->url = $url;
        $this->prefix = $prefix;
    }

    public function compile(Compiler $compiler, $indent = 0)
    {
        $compiler->addTraceInfo($this, $indent);
        $compiler->raw("echo dirname(");
        $this->url->compile($compiler);
        $compiler->raw(").'/'.");
        $this->prefix->compile($compiler);
        $compiler->raw(".'_'.");
        $compiler->raw("basename(");
        $this->url->compile($compiler);
        $compiler->raw(");");
        $compiler->raw("\n");
    }
}
