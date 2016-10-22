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

namespace Webuni\ComposerYamlPlugin;

use Composer\Command\BaseCommand;
use Composer\Json\JsonFile;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

final class YamlConvertCommand extends BaseCommand
{
    private $formats = [
        'yaml' => 'yaml',
        'yml' => 'yaml',
        'json' => 'json',
    ];

    protected function configure()
    {
        $this
            ->setName('yaml-convert')
            ->setDescription('Converts a composer.yml to json or vice-versa')
            ->addArgument('input', InputArgument::OPTIONAL, 'The input file', 'composer.yml')
            ->addArgument('output', InputArgument::OPTIONAL, 'The output file', 'composer.json')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $data = [
            'input' => $input->getArgument('input'),
            'output' => $input->getArgument('output'),
        ];

        if (!is_file($data['input'])) {
            throw new \InvalidArgumentException(sprintf('The input file "%s" does not exist.', $data['input']));
        }

        $formats = [];
        foreach ($data as $type => $file) {
            $format = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (!isset($this->formats[$format])) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid %s format "%s", must be one of: %s',
                    $type, $format, implode(', ', array_keys($this->formats))
                ));
            }

            $formats[$type] = $this->formats[$format];
        }

        if ($formats['input'] === $formats['output']) {
            throw new \InvalidArgumentException('Input format is same as output format.');
        }

        $content = file_get_contents($data['input']);

        if ('json' === $formats['input']) {
            $converted = Yaml::dump(JsonFile::parseJson($content));
        } else {
            $converted = JsonFile::encode(Yaml::parse($content));
        }

        file_put_contents($data['output'], $converted);
        $output->writeln(sprintf('Converted "%s" to "%s"', $data['input'], $data['output']));
    }
}
