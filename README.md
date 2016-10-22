Composer YAML Plugin
====================

This plugin allows you to convert a composer.yml file into composer.json format.
It will use those exact filenames of your current working directory.

Warning: If you already have a composer.json file, it will overwrite it.

Installation
------------

    composer global require webuni/composer-yaml-plugin 

Usage
-----

To convert from yaml to json, run:

    $ composer yaml-convert

To convert from json to yaml, run:

    $ composer yaml-convert composer.json composer.yml

Alternatives
------------

- https://github.com/igorw/composer-yaml

License
-------

MIT License. See the LICENSE file.
