<?php
/**
 * Created by PhpStorm.
 * User: max
 * Date: 28/08/16
 * Time: 11:53
 */

namespace Mindy\Template\Tests;

use Mindy\Template\Finder;
use Mindy\Template\Finder\AppTemplateFinder;
use Mindy\Template\Finder\ThemeTemplateFinder;
use Mindy\Template\Finder\TemplateFinder;

class FinderTest extends \PHPUnit_Framework_TestCase
{
    public function testFinder()
    {
        $finder = new Finder([
            'finders' => [
                new TemplateFinder([
                    'basePath' => __DIR__
                ]),
                new AppTemplateFinder([
                    'basePath' => __DIR__ . '/Modules',
                ]),
                new ThemeTemplateFinder([
                    'basePath' => __DIR__,
                    'theme' => 'default'
                ])
            ]
        ]);
        $this->assertEquals([
            __DIR__ . '/templates',
            __DIR__ . '/Modules/Core/templates',
            __DIR__ . '/themes/default/templates'
        ], $finder->getPaths());

        $this->assertEquals(__DIR__ . '/Modules/Core/templates/core/index.html', $finder->find('core/index.html'));
        $this->assertNull($finder->find('core/unknown.html'));
    }

    public function testTemplateFinder()
    {
        $finder = new TemplateFinder([
            'basePath' => __DIR__
        ]);
        $this->assertEquals([
            __DIR__ . '/templates'
        ], $finder->getPaths());

        $this->assertEquals(__DIR__ . '/templates/global_base.html', $finder->find('global_base.html'));
        $this->assertNull($finder->find('unknown.html'));
    }

    public function testAppTemplateFinder()
    {
        $finder = new AppTemplateFinder([
            'basePath' => __DIR__ . '/Modules',
        ]);
        $this->assertEquals([
            __DIR__ . '/Modules/Core/templates'
        ], $finder->getPaths());

        $this->assertEquals(__DIR__ . '/Modules/Core/templates/core/index.html', $finder->find('core/index.html'));
        $this->assertNull($finder->find('core/unknown.html'));
    }

    public function testThemeTemplateFinder()
    {
        $finder = new ThemeTemplateFinder([
            'basePath' => __DIR__,
            'theme' => 'default'
        ]);
        $this->assertEquals([
            __DIR__ . '/themes/default/templates'
        ], $finder->getPaths());

        $this->assertEquals(__DIR__ . '/themes/default/templates/main.html', $finder->find('main.html'));
        $this->assertNull($finder->find('unknown.html'));

        $finder = new ThemeTemplateFinder([
            'basePath' => __DIR__,
            'theme' => function () {
                return 'default';
            }
        ]);
        $this->assertEquals([
            __DIR__ . '/themes/default/templates'
        ], $finder->getPaths());

        $this->assertEquals(__DIR__ . '/themes/default/templates/main.html', $finder->find('main.html'));
        $this->assertNull($finder->find('unknown.html'));
    }
}