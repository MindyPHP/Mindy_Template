<?php

declare(strict_types = 1);

namespace Mindy\Template;
use function Mindy\app;

/**
 * Class Renderer
 * @package Mindy\Template
 */
class Renderer extends Loader
{
    public function __construct(array $options)
    {
        if (!isset($options['source'])) {
            $options['source'] = app()->finder->getPaths();
        }
        parent::__construct($options);
    }
}
