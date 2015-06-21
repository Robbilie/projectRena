<?php

namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CCPDataTask
 *
 * @package ProjectRena\Task
 */
class CCPDataTask extends Command
{
				/**
				 *
				 */
				protected function configure()
				{
								$this->setName('ccp:data')->setDescription('Updates the CCP database tables');
				}

				/**
				 * @param InputInterface $input
				 * @param OutputInterface $output
				 *
				 * @return int|null|void
				 */
				protected function execute(InputInterface $input, OutputInterface $output)
				{
								$app = RenaApp::getInstance();

								// Setup the url and cache path
								$url = "https://www.fuzzwork.co.uk/dump/";
								$cache = __DIR__ . "/../../cache/update";

								// Create the cache dir if it doesn't exist
								if(!file_exists($cache)) mkdir($cache);

								// Fetch the md5
								$md5file = "mysql-latest.tar.bz2.md5";
								$md5 = explode(" ", $app->cURL->getData($url . $md5file, 0))[0];

								$lastSeenMD5 = $app->Storage->get("ccpdataMD5");

								if($lastSeenMD5 != $md5)
								{
												$dbFiles = array(
													"dgmAttributeCategories",
													"dgmAttributeTypes",
													"dgmEffects",
													"dgmTypeAttributes",
													"dgmTypeEffects",
													"invFlags",
													"invGroups",
													"invTypes",
													"mapDenormalize",
													"mapRegions",
													"mapSolarSystems",
												);
												$type = ".sql.bz2";

												foreach($dbFiles as $file)
												{
																$dataURL = $url . "latest/" . $file . $type;
																try
																{
																				exec("wget -q {$dataURL} -O $cache/$file$type");
																				exec("bzip2 -q -d $cache/$file$type");
																} catch(\Exception $e)
																{
																				echo "Error";
																}

																$data = file_get_contents($cache . "/" . $file . ".sql");
																$data = str_replace("ENGINE=InnoDB", "ENGINE=TokuDB", $data);
																$dataParts = explode(";\n", $data);
																foreach($dataParts as $qry)
																{
																				$query = $qry . ";";
																				$app->Db->execute($query);
																}

																unlink("{$cache}/{$file}.sql");
												}

												$app->Storage->set("ccpdataMD5", $md5);
								}
				}
}