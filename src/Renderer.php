<?php

declare(strict_types = 1);

namespace Mindy\Template;

/**
 * Class Renderer
 * @package Mindy\Template
 */
class Renderer extends Loader
{
    public function __construct(array $options)
    {
        if (isset($options['finder'])) {
            $options['source'] = $options['finder']->getPaths();
        }
        parent::__construct($options);
    }
}
