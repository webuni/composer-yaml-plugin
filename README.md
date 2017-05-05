Composer YAML Plugin
====================

[![Packagist](https://img.shields.io/packagist/v/webuni/composer-yaml-plugin.svg?style=flat-square)](https://packagist.org/packages/webuni/composer-yaml-plugin)
[![Build Status](https://travis-ci.org/webuni/composer-yaml-plugin.svg?branch=master)](https://travis-ci.org/webuni/composer-yaml-plugin)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/webuni/composer-yaml-plugin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webuni/composer-yaml-plugin/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/webuni/composer-yaml-plugin/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/webuni/composer-yaml-plugin/?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/8fbbdeb9-d7ba-4c5c-8d88-db950a668265.svg?style=flat-square)](https://insight.sensiolabs.com/projects/8fbbdeb9-d7ba-4c5c-8d88-db950a668265)

This plugin allows you to convert a composer.yaml file into composer.json format.
It will use those exact filenames of your current working directory.

Warning: If you already have a composer.json file, it will overwrite it.

Installation
------------

    composer global require webuni/composer-yaml-plugin 

Usage
-----

To convert from yaml (`composer.yaml` or `composer.yam`) to json (`composer.json`), run:

    $ composer yaml-convert

To convert from json to yaml, run:

    $ composer yaml-convert composer.json

Alternatives
------------

- https://github.com/igorw/composer-yaml

License
-------

MIT License. See the LICENSE file.
