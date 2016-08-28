<?php
/**
 * Author: Falaleev Maxim
 * Email: max@studio107.ru
 * Company: http://en.studio107.ru
 * Date: 17/03/16
 * Time: 13:53
 */

namespace Mindy\Template;

use Exception;
use Mindy\Template\Finder\ITemplateFinder;
use Mindy\Template\Finder\ThemeTemplateFinder;

/**
 * Class Finder
 * @package Mindy\Finder
 */
class Finder
{
    /**
     * Template finders
     * @var \Mindy\Template\Finder\ITemplateFinder[]
     */
    private $_finders = [];
    /**
     * @var array of string
     */
    private $_paths = [];

    /**
     * Finder constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            if (method_exists($this, 'set' . ucfirst($key))) {
                $this->{'set' . ucfirst($key)}($value);
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * @param array $finders
     * @throws Exception
     */
    protected function setFinders(array $finders = [])
    {
        foreach ($finders as $config) {
            if (is_object($config)) {
                $finder = $config;
            } else {
                $finder = Creator::createObject($config);
            }

            if (($finder instanceof ITemplateFinder) === false) {
                throw new Exception("Unknown template finder");
            }

            $this->_finders[] = $finder;
        }
    }

    /**
     * @param $templatePath
     * @return mixed
     */
    public function find($templatePath)
    {
        /** @var \Mindy\Template\Finder\ITemplateFinder $finder */
        $templates = [];
        foreach ($this->_finders as $finder) {
            $template = $finder->find($templatePath);
            if ($template !== null) {
                $templates[] = $template;
            }
        }
        return array_shift($templates);
    }

    /**
     * @return array of string
     */
    public function getPaths()
    {
        if (empty($this->_paths)) {
            foreach ($this->_finders as $finder) {
                $this->_paths = array_merge($this->_paths, $finder->getPaths());
            }
        }
        return $this->_paths;
    }
}
