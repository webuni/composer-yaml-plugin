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

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webuni\ComposerYamlPlugin\YamlConvertCommand;

final class YamlConvertCommandTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $output;
    private $cwd;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->setAutoExit(false);
        $this->app->add(new YamlConvertCommand());
        $this->output = new BufferedOutput();

        $this->cwd = getcwd();
        chdir(sys_get_temp_dir());
        @unlink('composer.json');
        @unlink('composer.yml');
    }

    protected function tearDown()
    {
        chdir(sys_get_temp_dir());
        @unlink('composer.json');
        @unlink('composer.yml');
        chdir($this->cwd);
    }

    public function testDefaultInputFileDoesNotExists()
    {
        $this->app->run(new ArrayInput(['yaml-convert']), $this->output);
        $this->assertContains('The input file "composer.yml" does not exist.', $this->output->fetch());
    }

    public function testCustomInputFileDoesNotExists()
    {
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.json']), $this->output);
        $this->assertContains('The input file "composer.json" does not exist.', $this->output->fetch());
    }

    public function testSameFormat()
    {
        file_put_contents('composer.yml', 'name: package');
        $this->app->run(new ArrayInput(['yaml-convert', 'output' => 'composer.yml']), $this->output);
        $this->assertContains('Input format is same as output format.', $this->output->fetch());
    }

    public function testConvertYamlToJson()
    {
        file_put_contents('composer.yml', 'name: package');
        $this->app->run(new ArrayInput(['yaml-convert']), $this->output);
        $this->assertContains('Converted "composer.yml" to "composer.json"', $this->output->fetch());
        $this->assertFileExists('composer.json');
        $this->assertEquals("{\n    \"name\": \"package\"\n}", file_get_contents('composer.json'));
    }

    public function testConvertJsonToYaml()
    {
        file_put_contents('composer.json', "{\n    \"name\": \"package\"\n}");
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.json', 'output' => 'composer.yml']), $this->output);
        $this->assertContains('Converted "composer.json" to "composer.yml"', $this->output->fetch());
        $this->assertFileExists('composer.yml');
        $this->assertEquals("name: package\n", file_get_contents('composer.yml'));
    }
}
