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
 * @date 01/08/14.08.2014 16:20
 */

namespace Mindy\Template;


use Mindy\Base\Mindy;
use Mindy\Helper\Alias;

class Renderer extends Loader
{
    public function __construct(array $options = [])
    {
        if (!isset($options['target'])) {
            $options['target'] = Alias::get('application.runtime.cache');
        }
        if (!isset($options['source'])) {
            $options['source'] = Mindy::app()->finder->getPaths();
        }
        parent::__construct($options);
    }
}
