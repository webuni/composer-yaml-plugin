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
            ->setDescription('Converts a composer.yaml to json or vice-versa')
            ->addArgument('input', InputArgument::OPTIONAL, 'The input file')
            ->addArgument('output', InputArgument::OPTIONAL, 'The output file')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $this->getFiles([
            'input' => $input->getArgument('input'),
            'output' => $input->getArgument('output'),
        ]);

        $from = $this->getFormat($files['input'], 'input');

        if (!is_file($files['input'])) {
            throw new \InvalidArgumentException(sprintf('The input file "%s" does not exist.', $files['input']));
        }

        if (null === $files['output']) {
            $files['output'] = 'json' === $from ? 'composer.yaml' : 'composer.json';
        } else {
            $to = $this->getFormat($files['output'], 'output');

            if ($from === $to) {
                throw new \InvalidArgumentException(sprintf('Input format "%s" is same as output format.', $from));
            }
        }

        $content = file_get_contents($files['input']);

        if ('json' === $from) {
            $converted = Yaml::dump(JsonFile::parseJson($content));
        } else {
            $converted = JsonFile::encode(Yaml::parse($content));
        }

        file_put_contents($files['output'], $converted);
        $output->writeln(sprintf('Converted "%s" to "%s"', $files['input'], $files['output']));
    }

    private function getFiles(array $data)
    {
        if ((null === $data['input']) && (null === $data['output'])) {
            $data['output'] = 'composer.json';
        }

        if ((null === $data['input']) && is_file('composer.yml')) {
            $data['input'] = 'composer.yml';
        }

        if (null === $data['input']) {
            $data['input'] = 'composer.yaml';
        }

        return $data;
    }

    private function getFormat($file, $type)
    {
        $format = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!isset($this->formats[$format])) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid %s format "%s", must be one of: %s.',
                $type, $format, implode(', ', array_keys($this->formats))
            ));
        }

        return $this->formats[$format];
    }
}
