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
 * Class AppTemplateFinder
 * @package Mindy\Finder
 */
class AppTemplateFinder extends TemplateFinder
{
    /**
     * @param $templatePath
     * @return null|string absolute path of template if founded
     */
    public function find($templatePath)
    {
        $tmp = explode(DIRECTORY_SEPARATOR, $templatePath);
        if (count($tmp) > 1) {
            $path = join(DIRECTORY_SEPARATOR, [$this->basePath, ucfirst(array_shift($tmp)), $this->templatesDir, $templatePath]);
            if (is_file($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * @return array of available template paths
     */
    public function getPaths()
    {
        return glob($this->basePath . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . $this->templatesDir);
    }
}
