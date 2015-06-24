<?php

namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BorisTask
 *
 * @package ProjectRena\Task
 */
class BorisTask extends Command
{
				/**
				 *
				 */
				protected function configure()
				{
								$this->setName('boris')->setDescription('Starts boris');
				}

				/**
				 * @param InputInterface $input
				 * @param OutputInterface $output
				 *
				 * @return int|null|void
				 */
				protected function execute(InputInterface $input, OutputInterface $output)
				{
								$boris = new \Boris\Boris('Rena> ');
								$boris->onStart('$app = \ProjectRena\RenaApp::getInstance();');
								$boris->onStart('$db = $app->Db;');
								$boris->onStart('$cache = $app->Cache;');
								$boris->
								$boris->start();
				}
}
