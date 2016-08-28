<?php
/**
 * Author: Falaleev Maxim
 * Email: max@studio107.ru
 * Company: http://en.studio107.ru
 * Date: 17/03/16
 * Time: 13:53
 */

namespace Mindy\Template\Finder;

/**
 * Class BaseTemplateFinder
 * @package Mindy\Finder\Finder
 */
abstract class BaseTemplateFinder implements ITemplateFinder
{
    /**
     * @var string
     */
    public $basePath;

    public function __construct(array $config)
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    /**
     * @param $templatePath
     * @return null|string absolute path of template if founded
     */
    abstract public function find($templatePath);

    /**
     * @return array of available template paths
     */
    abstract public function getPaths();
}
