<?php
namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
use Sami\Sami;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class UpdateTask
 *
 * @package ProjectRena\Task
 */
class UpdateTask extends Command
{
				/**
				 *
				 */
				protected function configure()
				{
								$this->setName("update")
												->setDescription("Updates the project (Composer and RenaApp)");
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
								// Check if composer is in the dir
								if(!file_exists(__DIR__ . "/../../composer.phar"))
								{
												$output->writeln("Composer doesn't exist, downloading it");
												$composer = $app->cURL->getData("https://getcomposer.org/composer.phar", 0);
												file_put_contents(__DIR__ . "/../../composer.phar", $composer);
								}
								exec("php " . __DIR__ . "/../../composer.phar update -o");
								$output->writeln("Updating composer");

								// Update RenaApp
								$load = array(
									__DIR__ . "/../Lib/*.php",
									__DIR__ . "/../Lib/*/*.php",
									__DIR__ . "/../Lib/*/*/*.php",
									__DIR__ . "/../Model/*.php",
									__DIR__ . "/../Model/*/*.php",
									__DIR__ . "/../Model/*/*/*.php",
								);
								// Generate RenaApp.php
								$php = "<?php\n";
								$php .= "\n";
								$php .= "namespace ProjectRena;\n";
								$php .= "\n";
								$php .= $this->generateNamespace();
								$php .= "\n";
								$php .= "/**\n";
								$php .= $this->generateProperty();
								$php .= " */\n";
								$php .= "\n";
								$php .= "class RenaApp extends Slim\n";
								$php .= "{\n";
								$php .= "}";
								$output->writeln("Generating RenaApp");
								file_put_contents(__DIR__ . "/../RenaApp.php", $php);

								// Do more stuff (Clear cache?)
				}

				/**
				 * @return string
				 */
				private function generateNamespace()
				{
								$internal = "use Slim\\Slim;\n";
								$load = array(
									__DIR__ . "/../Lib/*.php",
									__DIR__ . "/../Lib/*/*.php",
									__DIR__ . "/../Lib/*/*/*.php",
									__DIR__ . "/../Model/*.php",
									__DIR__ . "/../Model/*/*.php",
									__DIR__ . "/../Model/*/*/*.php",
								);
								foreach($load as $path)
								{
												$files = glob($path);
												foreach($files as $file)
												{
																$exp = explode("/../", $file);
																$basename = basename($file);
																$callName = str_replace(".php", "", $basename);
																if(stristr($file, "EVEApi"))
																{
																				$ep = explode("/EVEApi/", $file);
																				$ep = explode("/", $ep[1]);
																				$namespace = "use ProjectRena\\" . str_replace(".php", "", str_replace("/", "\\", $exp[1])) . " as EVE" . $ep[0] . $callName . ";";
																} else
																{
																				$namespace = "use ProjectRena\\" . str_replace(".php", "", str_replace("/", "\\", $exp[1])) . ";";
																}
																$internal .= $namespace . "\n";
												}
								}

								return $internal;
				}


				/**
				 * @return string
				 */
				private function generateProperty()
				{
								$internal = "";
								$load = array(
									__DIR__ . "/../Lib/*.php",
									__DIR__ . "/../Lib/*/*.php",
									__DIR__ . "/../Lib/*/*/*.php",
									__DIR__ . "/../Model/*.php",
									__DIR__ . "/../Model/*/*.php",
									__DIR__ . "/../Model/*/*/*.php",
								);
								foreach($load as $path)
								{
												$files = glob($path);
												foreach($files as $file)
												{
																$basename = basename($file);
																$callName = str_replace(".php", "", $basename);
																if(stristr($file, "EVEApi"))
																{
																				$ep = explode("/EVEApi/", $file);
																				$ep = explode("/", $ep[1]);
																				$callName = "EVE" . $ep[0] . $callName;
																}
																$internal .= " * @property " . $callName . " " . $callName . "\n";
												}
								}

								return $internal;
				}
}
