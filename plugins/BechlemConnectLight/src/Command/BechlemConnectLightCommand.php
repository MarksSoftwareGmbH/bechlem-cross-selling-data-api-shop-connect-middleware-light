<?php
declare(strict_types=1);

/* 
 * MIT License
 *
 * Copyright (c) 2018-present, Marks Software GmbH (https://www.marks-software.de/)
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace BechlemConnectLight\Command;

use Cake\Cache\Cache;
use Cake\Cache\Engine\ApcuEngine;
use Cake\Cache\Engine\WincacheEngine;
use Cake\Cache\InvalidArgumentException;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\CommandCollection;
use Cake\Console\CommandCollectionAwareInterface;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Text;

/**
 * BechlemConnectLight command.
 */
class BechlemConnectLightCommand extends Command implements CommandCollectionAwareInterface
{
    /**
     * The command collection to get help on.
     *
     * @var \Cake\Console\CommandCollection
     */
    protected array $commands;

    /**
     * @inheritDoc
     */
    public function setCommandCollection(CommandCollection $commands): void
    {
        $this->commands = $commands;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->warning('No command provided. Run `bechlem_connect_light --help` to get a list of commands.');

        return static::CODE_ERROR;
    }

    /**
     * @inheritDoc
     */
    protected function displayHelp(ConsoleOptionParser $parser, Arguments $args, ConsoleIo $io): void
    {
        $io->out(Text::wrap($parser->getDescription(), 72), 2);
        $io->info('Available Commands:', 2);

        foreach ($this->commands as $command => $class) {
            if (substr($command, 0, 22) === 'bechlem_connect_light.') {
                $io->out('- ' . $command);
            }
        }

        $io->out();
        $io->out('To run a command, type <info>`bechlem_connect_light command_name [args|options]`</info>');
        $io->out('To get help on a specific command, type <info>`bechlem_connect_light command_name --help`</info>', 2);
    }

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription(
                'The BechlemConnectLight console provides commands for running command ' .
                'and cron jobs, and for modifying your application data to use BechlemConnectLight.'
            );
    }
}
