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
 * @date 03/08/14.08.2014 18:31
 */

namespace Mindy\Template;

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
