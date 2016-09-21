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

declare(strict_types = 1);

namespace Mindy\Template;

use Mindy\Template\Expression\ArrayExpression;
use Mindy\Template\Node\CsrfTokenNode;
use Mindy\Template\Node\ImageNode;
use Mindy\Template\Node\UrlNode;

class DefaultLibrary extends Library
{
    /**
     * @return array
     */
    public function getHelpers() : array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getTags() : array
    {
        return [

        ];
    }
}