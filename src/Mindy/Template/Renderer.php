<?php

namespace Mindy\Template;

use Mindy\Base\Mindy;
use Mindy\Helper\Alias;

/**
 * Class Renderer
 * @package Mindy\Template
 */
class Renderer extends Loader
{
    public function __construct(array $options = [])
    {
        if (!isset($options['target'])) {
            $options['target'] = Alias::get('application.runtime.cache_templates');
        }
        if (!isset($options['source'])) {
            $options['source'] = Mindy::app()->finder->getPaths();
        }
        parent::__construct($options);
    }
}
