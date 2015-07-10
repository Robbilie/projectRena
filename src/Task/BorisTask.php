<?php

namespace ProjectRena\Task;

use Cilex\Command\Command;
use DateTime;
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
								/*
								$app = \ProjectRena\RenaApp::getInstance();
								$mails = $app->Db->query("SELECT * FROM killmails WHERE source = 'zkillboardRedisQ'");

								foreach($mails as $mail)
								{
												$json = json_decode($mail["kill_json"], true);
												$killID = $json["killID"];
												if(!stristr($json["killTime"], "."))
																continue;
												$date = DateTime::createFromFormat("Y.m.d H:i:s", $json["killTime"]);
												$json["killTime"] = $date->format("Y-m-d H:i:s");
												$json = json_encode($json);
												$app->Db->execute("UPDATE killmails SET kill_json = :kill_json WHERE killID = :killID", array(":kill_json" => $json, ":killID" => $killID));
								}*/
								$boris = new \Boris\Boris('Rena> ');
								$boris->onStart('$app = \ProjectRena\RenaApp::getInstance();');
								$boris->onStart('$db = $app->Db;');
								$boris->onStart('$cache = $app->Cache;');
								$boris->start();
				}
}
