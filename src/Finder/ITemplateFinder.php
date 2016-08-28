<?php
/**
 * Author: Falaleev Maxim
 * Email: max@studio107.ru
 * Company: http://en.studio107.ru
 * Date: 17/03/16
 * Time: 13:59
 */

namespace Mindy\Template\Finder;

interface ITemplateFinder
{
    /**
     * @param $templatePath
     * @return null|string absolute path of template if founded
     */
    public function find($templatePath);

    /**
     * @return array of available template paths
     */
    public function getPaths();
}