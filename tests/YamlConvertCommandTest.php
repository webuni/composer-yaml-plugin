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

use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Webuni\ComposerYamlPlugin\YamlConvertCommand;

final class YamlConvertCommandTest extends TestCase
{
    private $app;
    private $buffer;
    private $cwd;

    protected function setUp()
    {
        $this->app = new Application();
        $this->app->setAutoExit(false);
        $this->app->add(new YamlConvertCommand());
        $this->buffer = new BufferedOutput();

        $this->cwd = getcwd();
        chdir(sys_get_temp_dir());
        $this->removeFiles();
    }

    protected function tearDown()
    {
        chdir($this->cwd);
        $this->removeFiles();
    }

    public function testDefaultInputFileDoesNotExists()
    {
        $this->app->run(new ArrayInput(['yaml-convert']), $this->buffer);
        $this->assertContains('The input file "composer.yaml" does not exist.', $this->buffer->fetch());
    }

    public function testCustomInputFileDoesNotExists()
    {
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.json']), $this->buffer);
        $this->assertContains('The input file "composer.json" does not exist.', $this->buffer->fetch());
    }

    public function testInvalidFormat()
    {
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.neon']), $this->buffer);
        $this->assertContains('Invalid input format "neon", must be one of: yaml, yml, json.', $this->buffer->fetch());
    }

    public function testSameFormat()
    {
        file_put_contents('composer.yml', 'name: package');
        $this->app->run(new ArrayInput(['yaml-convert', 'output' => 'composer.yaml']), $this->buffer);
        $this->assertContains('Input format "yaml" is same as output format.', $this->buffer->fetch());
    }

    public function testConvertYamlToJson()
    {
        file_put_contents('composer.yaml', 'name: package');
        $this->app->run(new ArrayInput(['yaml-convert']), $this->buffer);
        $this->assertContains('Converted "composer.yaml" to "composer.json"', $this->buffer->fetch());
        $this->assertFileExists('composer.json');
        $this->assertEquals("{\n    \"name\": \"package\"\n}", file_get_contents('composer.json'));
    }

    public function testConvertYmlToJson()
    {
        file_put_contents('composer.yml', 'name: package');
        $this->app->run(new ArrayInput(['yaml-convert']), $this->buffer);
        $this->assertContains('Converted "composer.yml" to "composer.json"', $this->buffer->fetch());
        $this->assertFileExists('composer.json');
        $this->assertEquals("{\n    \"name\": \"package\"\n}", file_get_contents('composer.json'));
    }

    public function testConvertJsonToYaml()
    {
        file_put_contents('composer.json', "{\n    \"name\": \"package\"\n}");
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.json']), $this->buffer);
        $this->assertContains('Converted "composer.json" to "composer.yaml"', $this->buffer->fetch());
        $this->assertFileExists('composer.yaml');
        $this->assertEquals("name: package\n", file_get_contents('composer.yaml'));
    }

    public function testConvertJsonToYml()
    {
        file_put_contents('composer.json', "{\n    \"name\": \"package\"\n}");
        $this->app->run(new ArrayInput(['yaml-convert', 'input' => 'composer.json', 'output' => 'composer.yml']), $this->buffer);
        $this->assertContains('Converted "composer.json" to "composer.yml"', $this->buffer->fetch());
        $this->assertFileExists('composer.yml');
        $this->assertEquals("name: package\n", file_get_contents('composer.yml'));
    }

    private function removeFiles()
    {
        foreach (['json', 'yaml', 'yml'] as $extension) {
            $file = sys_get_temp_dir().'/composer.'.$extension;
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
