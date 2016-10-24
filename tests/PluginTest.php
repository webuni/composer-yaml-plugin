<?php

/*
 * This is part of the webuni/composer-yaml-plugin package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\ComposerYamlPlugin\Tests;

use Composer\Composer;
use Composer\Console\Application;
use Composer\IO\BufferIO;
use Composer\Plugin\PluginManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webuni\ComposerYamlPlugin\Plugin;

final class PluginTest extends \PHPUnit_Framework_TestCase
{
    private $plugin;

    protected function setUp()
    {
        $this->plugin = new Plugin();
    }

    public function testAddNewCommand()
    {
        $pm = $this
            ->getMockBuilder(PluginManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPlugins'])
            ->getMock()
        ;

        $pm
            ->expects($this->any())
            ->method('getPlugins')
            ->willReturn([$this->plugin])
        ;

        $composer = $this->getMockBuilder(Composer::class)->getMock();
        $composer
            ->expects($this->any())
            ->method('getPluginManager')
            ->willReturn($pm)
        ;

        $this->plugin->activate($composer, new BufferIO());

        $app = $this
            ->getMockBuilder(Application::class)
            ->setMethods(['getComposer'])
            ->getMock()
        ;

        $app
            ->expects($this->any())
            ->method('getComposer')
            ->willReturn($composer)
        ;

        $output = new BufferedOutput();

        $app->doRun(new ArrayInput(['-d' => sys_get_temp_dir()]), $output);
        $this->assertRegExp('/yaml-convert +Converts a composer\.yml to json or vice-versa/', $output->fetch());
    }
}
