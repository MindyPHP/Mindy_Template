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
 * @date 03/08/14.08.2014 18:33
 */

namespace Mindy\Template;


use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node\CsrfTokenNode;
use Mindy\Template\Node\UrlNode;

class DefaultLibrary extends Library
{
    /**
     * @return array
     */
    public function getHelpers()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return [
            'url' => 'parseUrl',
            'csrf_token' => 'parseCsrfToken',
        ];
    }

    public function parseCsrfToken($token)
    {
        $this->stream->expect(Token::BLOCK_END);
        return new CsrfTokenNode($token->getLine());
    }

    public function parseUrl($token)
    {
        $name = null;
        $params = array();
        $route = $this->parser->parseExpression();
        while (
            (
                $this->stream->test(Token::NAME) ||
                $this->stream->test(Token::NUMBER) ||
                $this->stream->test(Token::STRING)
            ) && !$this->stream->test(Token::BLOCK_END)
        ) {
            if ($this->stream->test(Token::NAME) && $this->stream->look()->test(Token::OPERATOR, '=')) {
                $key = $this->parser->parseName()->getValue();
                $this->stream->next();
                $params[$key] = $this->parser->parseExpression();
            } else if ($this->stream->test(Token::NAME, 'as')) {
                $this->stream->next();
                $name = $this->parser->parseName()->getValue();
            } else if ($this->stream->test(Token::NAME)) {
                $expression = $this->parser->parseExpression();
                if($expression instanceof ArrayExpression) {
                    $params = $expression;
                    break;
                } else {
                    $params[] = $expression;
                }
            } else {
                $params[] = $this->parser->parseExpression();
            }
        }

        $this->stream->expect(Token::BLOCK_END);
        return new UrlNode($token->getLine(), $route, $params, $name);
    }
}