<?php

namespace ProjectRena\Task;

use gossi\codegen\generator\CodeFileGenerator;
use gossi\codegen\model\PhpProperty;
use ProjectRena\Lib;
use Cilex\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use gossi\codegen\model\PhpClass;
use gossi\codegen\model\PhpMethod;
use gossi\codegen\model\PhpParameter;

/**
 * Class GeneratorTask
 *
 * @package ProjectRena\Task
 */
class GeneratorTask extends Command
{
				private $descr;
				/**
				 *
				 */
				protected function configure()
				{
								$this->setName('generator')
									->setDescription('Generates Controllers / Models / Libs / Tasks / Cronjobs / Resque Queues')
									->addOption("controller", "c", InputOption::VALUE_NONE, "Create Controller")
									->addOption("model", "m", InputOption::VALUE_NONE, "Create Model")
									->addOption("libs", "l", InputOption::VALUE_NONE, "Create Lib")
									->addOption("task", "t", InputOption::VALUE_NONE, "Create Task")
									->addOption("cronjob", "j", InputOption::VALUE_NONE, "Create Cronjob")
									->addOption("resque", "r", InputOption::VALUE_NONE, "Create Resque Queue");
				}

				/**
				 * @param InputInterface $input
				 * @param OutputInterface $output
				 *
				 * @return int|null|void
				 */
				protected function execute(InputInterface $input, OutputInterface $output)
				{
								$name = prompt("Name");
								$this->descr["description"] = prompt("Description");

								if(!$name)
								{
												$output->writeln("Error, name is not supplied.");
												exit();
								}

								if($input->getOption("controller"))
												$this->controller($name, $output);

								if($input->getOption("model"))
												$this->model($name, $output);

								if($input->getOption("libs"))
												$this->libs($name, $output);

								if($input->getOption("task"))
												$this->task($name, $output);

								if($input->getOption("cronjob"))
												$this->cronjob($name, $output);

								if($input->getOption("resque"))
												$this->resque($name, $output);

								$output->writeln("Please run update to update RenaApp and Composer");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function controller($name, $output)
				{
								$path = __DIR__ . "/../Controller/{$name}Controller.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								// Create controller
								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Controller\\{$name}Controller")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("__construct")
																->addParameter(PhpParameter::create("app")
																				->setType("RenaApp")
																)
																->setBody("\$this->app = \$app;\n\$this->db = \$app->Db;\n\$this->config = \$app->baseConfig;\n\$this->cache = \$app->Cache;\n\$this->curl = \$app->cURL;\n\$this->statsd = \$app->StatsD;\n\$this->log = \$app->Logging;")
												)
												->setProperty(PhpProperty::create("app")
																->setVisibility("private")
																->setDescription("The Slim Application")
												)
												->setProperty(PhpProperty::create("db")
																->setVisibility("private")
																->setDescription("The Database")
												)
												->setProperty(PhpProperty::create("config")
																->setVisibility("private")
																->setDescription("The baseConfig (config/config.php)")
												)
												->setProperty(PhpProperty::create("cache")
																->setVisibility("private")
																->setDescription("The Cache")
												)
												->setProperty(PhpProperty::create("curl")
																->setVisibility("private")
																->setDescription("cURL interface (getData / setData)")
												)
												->setProperty(PhpProperty::create("statsd")
																->setVisibility("private")
																->setDescription("StatsD for tracking stats")
												)
												->setProperty(PhpProperty::create("log")
																->setVisibility("private")
																->setDescription("The logger, outputs to logs/app.log")
												)
												->declareUse('ProjectRena\RenaApp');

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);

								// Create twig file
								$twigFile = <<<EOF
{% set pageTitle = "{$name}" %}
{% extends 'base.twig' %}

{% block content %}
{% endblock %}
EOF;

								// Create files
								file_put_contents($path, $code);
								file_put_contents(__DIR__ . "/../../view/{$name}.twig", $twigFile);

								$output->writeln("Controller and twig file created: {$path}");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function model($name, $output)
				{
								$path = __DIR__ . "/../Model/{$name}.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								// Create model
								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Model\\{$name}")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("__construct")
																->addParameter(PhpParameter::create("app")
																				->setType("RenaApp")
																)
																->setBody("\$this->app = \$app;\n\$this->db = \$app->Db;\n\$this->config = \$app->baseConfig;\n\$this->cache = \$app->Cache;\n\$this->curl = \$app->cURL;\n\$this->statsd = \$app->StatsD;\n\$this->log = \$app->Logging;")
												)
												->setProperty(PhpProperty::create("app")
																->setVisibility("private")
																->setDescription("The Slim Application")
												)
												->setProperty(PhpProperty::create("db")
																->setVisibility("private")
																->setDescription("The Database")
												)
												->setProperty(PhpProperty::create("config")
																->setVisibility("private")
																->setDescription("The baseConfig (config/config.php)")
												)
												->setProperty(PhpProperty::create("cache")
																->setVisibility("private")
																->setDescription("The Cache")
												)
												->setProperty(PhpProperty::create("curl")
																->setVisibility("private")
																->setDescription("cURL interface (getData / setData)")
												)
												->setProperty(PhpProperty::create("statsd")
																->setVisibility("private")
																->setDescription("StatsD for tracking stats")
												)
												->setProperty(PhpProperty::create("log")
																->setVisibility("private")
																->setDescription("The logger, outputs to logs/app.log")
												)
												->declareUse('ProjectRena\RenaApp');

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);
								file_put_contents($path, $code);

								$output->writeln("Model created: {$path}");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function libs($name, $output)
				{
								$path = __DIR__ . "/../Lib/{$name}.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								// Create lib
								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Lib\\{$name}")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("__construct")
																->addParameter(PhpParameter::create("app")
																				->setType("RenaApp")
																)
																->setBody("\$this->app = \$app;\n\$this->db = \$app->Db;\n\$this->config = \$app->baseConfig;\n\$this->cache = \$app->Cache;\n\$this->curl = \$app->cURL;\n\$this->statsd = \$app->StatsD;\n\$this->log = \$app->Logging;")
												)
												->setProperty(PhpProperty::create("app")
																->setVisibility("private")
																->setDescription("The Slim Application")
												)
												->setProperty(PhpProperty::create("db")
																->setVisibility("private")
																->setDescription("The Database")
												)
												->setProperty(PhpProperty::create("config")
																->setVisibility("private")
																->setDescription("The baseConfig (config/config.php)")
												)
												->setProperty(PhpProperty::create("cache")
																->setVisibility("private")
																->setDescription("The Cache")
												)
												->setProperty(PhpProperty::create("curl")
																->setVisibility("private")
																->setDescription("cURL interface (getData / setData)")
												)
												->setProperty(PhpProperty::create("statsd")
																->setVisibility("private")
																->setDescription("StatsD for tracking stats")
												)
												->setProperty(PhpProperty::create("log")
																->setVisibility("private")
																->setDescription("The logger, outputs to logs/app.log")
												)
												->declareUse('ProjectRena\RenaApp');

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);
								file_put_contents($path, $code);

								$output->writeln("Lib created: {$path}");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function task($name, $output)
				{
								$path = __DIR__ . "/../Task/{$name}Task.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Task\\{$name}Task")
												->setParentClassName("Command")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("configure")
																->setVisibility("protected")
																->setBody("\$this->setName('$name')->setDescription('" . $this->descr["description"] . "');")
												)
												->setMethod(PhpMethod::create("execute")
																->setVisibility("protected")
																->addParameter(PhpParameter::create("input")->setType("InputInterface"))
																->addParameter(PhpParameter::create("output")->setType("OutputInterface"))
																->setBody("//Init rena\n\$app = RenaApp::geTInstance();")
												)
												->declareUses('ProjectRena\RenaApp', 'Cilex\Command\Command', 'ProjectRena\Lib', 'Symfony\Component\Console\Input\InputInterface', 'Symfony\Component\Console\Output\OutputInterface');

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);
								file_put_contents($path, $code);

								$output->writeln("Task created: {$path}");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function cronjob($name, $output)
				{
								$path = __DIR__ . "/../Task/Cronjobs/{$name}Cronjob.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Task\\Cronjobs\\{$name}Cronjob")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("getRunTimes")
																->setVisibility("public")
																->setStatic(true)
																->setDescription("Defines how often the cronjob runs, every 1 second, every 60 seconds, every 86400 seconds, etc.")
																->setBody("return 0; // Never runs")
												)
												->setMethod(PhpMethod::create("execute")
																->setVisibility("public")
																->setStatic(true)
																->setDescription("Executes the cronjob task")
																->addParameter(PhpParameter::create("pid"))
																->addParameter(PhpParameter::create("md5"))
																->addParameter(PhpParameter::create("app")->setType("RenaApp"))
																->setBody("exit(); // Keep this at the bottom, to make sure the fork exits")
												)
												->declareUse('ProjectRena\RenaApp');

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);
								file_put_contents($path, $code);

        $output->writeln("Cronjob created: {$path}");
				}

				/**
				 * @param $name
				 * @param OutputInterface $output
				 *
				 * @return mixed
				 */
				private function resque($name, $output)
				{
								$path = __DIR__ . "/../Task/Resque/{$name}Resque.php";
								if(file_exists($path))
								{
												return $output->writeln("Error, file already exists");
								}

								$class = new PhpClass();
								$class->setQualifiedName("ProjectRena\\Task\\Resque\\{$name}")
												->setDescription($this->descr)
												->setMethod(PhpMethod::create("setUp")
																->setVisibility("public")
																->setDescription("Sets up the task (Setup \$this->crap and such here)")
												)
												->setMethod(PhpMethod::create("perform")
																->setVisibility("public")
																->setDescription("Performs the task, can access all \$this->crap setup in setUp)")
												)
												->setMethod(PhpMethod::create("tearDown")
																->setVisibility("public")
																->setDescription("Tears the task down, unset \$this->crap and such")
												);

								$generator = new CodeFileGenerator();
								$code = $generator->generate($class);
								file_put_contents($path, $code);

								$output->writeln("Resque Queue created: {$path}");
				}
}
