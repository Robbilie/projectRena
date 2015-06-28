<?php

namespace ProjectRena\Task;

use Cilex\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ResqueCron.
 */
class ResqueTask extends Command
{
				/**
				 *
				 */
				protected function configure()
				{
								$this->setName('resque:run')->setDescription('Fires up resque');
				}

				/**
				 * @param InputInterface $input
				 * @param OutputInterface $output
				 *
				 * @return int|null|void
				 */
				protected function execute(InputInterface $input, OutputInterface $output)
				{
								putenv("VERBOSE=0");
								include __DIR__ . "/../../vendor/chrisboulton/php-resque/resque.php";
				}
}
