<?php

declare(strict_types = 1);

namespace Mindy\Template;
use Mindy\Finder\Finder;

/**
 * Class Renderer
 * @package Mindy\Template
 */
class Renderer extends Loader
{
    /**
     * Renderer constructor.
     * @param Finder $finder
     * @param array $options
     */
    public function __construct(Finder $finder, array $options = [])
    {
        parent::__construct(array_merge($options, ['source' => $finder->getPaths()]));
    }
}
