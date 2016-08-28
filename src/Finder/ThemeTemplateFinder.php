<?php
/**
 * Author: Falaleev Maxim
 * Email: max@studio107.ru
 * Company: http://en.studio107.ru
 * Date: 17/03/16
 * Time: 13:53
 */

namespace Mindy\Template\Finder;

use Closure;

/**
 * Class ThemeTemplateFinder
 * @package Mindy\Finder
 */
class ThemeTemplateFinder extends TemplateFinder
{
    /**
     * @var string
     */
    public $theme = 'default';

    /**
     * @param $templatePath
     * @return null|string absolute path of template if founded
     */
    public function find($templatePath)
    {
        $theme = $this->theme instanceof Closure ? $this->theme->__invoke() : $this->theme;
        $path = join(DIRECTORY_SEPARATOR, [$this->basePath, 'themes', $theme, $this->templatesDir, $templatePath]);
        if (is_file($path)) {
            return $path;
        }

        return null;
    }

    /**
     * @return array of available template paths
     */
    public function getPaths()
    {
        return [
            join(DIRECTORY_SEPARATOR, [$this->basePath, 'themes', $this->getTheme(), $this->templatesDir])
        ];
    }

    /**
     * @return string
     */
    public function getTheme()
    {
        $theme = $this->theme;
        if ($theme instanceof Closure) {
            $theme = $theme->__invoke();
        }
        return $theme;
    }
}
