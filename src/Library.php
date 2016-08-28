<?php

declare(strict_types = 1);

namespace Mindy\Template;

/**
 * Class Library
 * @package Mindy\Template
 */
abstract class Library
{
    /**
     * @var \Mindy\Template\Parser
     */
    protected $parser;
    /**
     * @var \Mindy\Template\TokenStream
     */
    protected $stream;

    /**
     * @return array
     */
    abstract public function getHelpers();

    /**
     * @return array
     */
    abstract public function getTags();

    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    public function setStream(TokenStream $stream)
    {
        $this->stream = $stream;
        return $this;
    }
}
