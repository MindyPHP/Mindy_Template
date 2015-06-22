<?php

namespace Mindy\Template;

use Mindy\Base\Mindy;
use Mindy\Helper\Alias;
use Mindy\Helper\Traits\Configurator;

/**
 * Class Renderer
 * @package Mindy\Template
 */
class Renderer extends Loader
{
    use Configurator;

    /**
     * @var \Mindy\Finder\Finder
     */
    public $finder = null;

    public function __construct(array $options = [])
    {
        if (!isset($options['target'])) {
            $options['target'] = Alias::get('application.runtime.cache_templates');
        }
        if (!isset($options['source']) && class_exists('\Mindy\Base\Mindy')) {
            $options['source'] = Mindy::app()->finder->getPaths();
        }
        if ($this->finder !== null) {
            $options['source'] = $this->finder->getPaths();
        }
        parent::__construct($options);
    }
}
