<?php

namespace Webuni\ComposerYamlPlugin\Tests;

require __DIR__.'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

if (class_exists(TestCase::class)) {
    class CompatibleTestsCase extends TestCase {}
} else {
    class CompatibleTestsCase extends \PHPUnit_Framework_TestCase {}
}
