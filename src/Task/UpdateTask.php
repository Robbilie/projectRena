<?php
namespace ProjectRena\Task;

use Cilex\Command\Command;
use ProjectRena\Lib;
use ProjectRena\RenaApp;
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
												->addOption("composer", "c", InputOption::VALUE_NONE, "Run Composer Update")
												->addOption("unittest", "u", InputOption::VALUE_NONE, "Run Unit Test")
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

								if($input->getOption("composer"))
								{
												// Check if composer is in the dir
												if(!file_exists(__DIR__ . "/../../composer.phar"))
												{
																$output->writeln("Composer doesn't exist, downloading it");
																$composer = $app->cURL->getData("https://getcomposer.org/composer.phar", 0);
																file_put_contents(__DIR__ . "/../../composer.phar", $composer);
												}
												$output->writeln("Updating composer");
												exec("php " . __DIR__ . "/../../composer.phar update -o");
								}

								// Run unit tests and update the documentation
								if($input->getOption("unittest"))
												$this->runUnitTest($output);

								// Generate an optimized loader
								$output->writeln("Generating Optimized Loader");
								$loaderCode = $this->generateOptimizedLoader($app);
								file_put_contents(__DIR__ . "/../OptimizedLoader.php", $loaderCode);

								// Update RenaApp
								$renaAppCode = $this->generateRenaApp();
								$output->writeln("Generating RenaApp");
								file_put_contents(__DIR__ . "/../RenaApp.php", $renaAppCode);
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

				private function generateRenaApp()
				{
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

								return $php;
				}

				private function runUnitTest($output)
				{
								// Check if phpunit.phar is in the dir
								if(!file_exists(__DIR__ . "/../../phpunit.phar"))
								{
												$output->writeln("PHPUnit doesn't exist, downloading it");
												$composer = $app->cURL->getData("https://phar.phpunit.de/phpunit.phar", 0);
												file_put_contents(__DIR__ . "/../../phpunit.phar", $composer);
								}
								$output->writeln("Running unit tests");
								chdir(__DIR__ . "/../../tests");
								exec("php ../phpunit.phar --coverage-html=/storage/www/projectRenaDocs/tests/ --bootstrap=init.php .");
								chdir("/storage/www/projectRenaDocs/");
								exec("git add *");
								exec("git commit -m 'Update tests'");
								exec("git push");
				}

				private function generateOptimizedLoader($app)
				{
								$php = "<?php\n";
								$php .= "\$app->container->singleton(\"baseConfig\", function(\$container) use (\$app)\n";
								$php .= "{\n";
								$php .= "	return new \\ProjectRena\\Lib\\baseConfig();\n";
								$php .= "});\n";

								// Load everything else
								// Paths to load files in
								$load = array(
												__DIR__ . "/../../src/Lib/*.php",
												__DIR__ . "/../../src/Lib/*/*.php",
												__DIR__ . "/../../src/Lib/*/*/*.php",
												__DIR__ . "/../../src/Model/*.php",
												__DIR__ . "/../../src/Model/*/*.php",
												__DIR__ . "/../../src/Model/*/*/*.php",
								);

								foreach($load as $path)
								{
												$files = glob($path);

												foreach($files as $file)
												{
																$exp = explode("/../../src/", $file);
																$basename = basename($file);
																$callName = str_replace(".php", "", $basename);
																$namespace = "\\ProjectRena\\" . str_replace(".php", "", str_replace("/", "\\", $exp[1]));

																if(stristr($file, "EVEApi"))
																{
																				$ep = explode("/EVEApi/", $file);
																				$ep = explode("/", $ep[1]);
																				$callName = "EVE" . $ep[0] . $callName;
																}

																if(method_exists(new $namespace($app), "RunAsNew"))
																{
																				$php .= "\n";
																				$php .= "\$app->container->set(\"{$callName}\", function(\$container) use (\$app)\n";
																				$php .= "{\n";
																				$php .= "		return new {$namespace}(\$app);\n";
																				$php .= "});\n";
																} else
																{
																				$php .= "\n";
																				$php .= "\$app->container->singleton(\"{$callName}\", function(\$container) use (\$app)\n";
																				$php .= "{\n";
																				$php .= "		return new {$namespace}(\$app);\n";
																				$php .= "});\n";
																}
												}
								}

								return $php;
				}
}
