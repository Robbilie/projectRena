<?php

// If the optimized loader exists, we'll just use that since it's faster than all the explodes and shit..
if(file_exists(__DIR__ . "/OptimizedLoader.php") && php_sapi_name() != "cli")
{
				require_once(__DIR__ . "/OptimizedLoader.php");
				return;
}

// Load baseConfig first..
$app->container->singleton(/**
 * @param $container
 *
 * @return \ProjectRena\Lib\baseConfig
 */
	"baseConfig", function ($container) use ($app)
{
				return new \ProjectRena\Lib\baseConfig();
});

// Load everything else
// Paths to load files in
$load = array(
	__DIR__ . "/../src/Lib/*.php",
	__DIR__ . "/../src/Lib/*/*.php",
	__DIR__ . "/../src/Lib/*/*/*.php",
	__DIR__ . "/../src/Model/*.php",
	__DIR__ . "/../src/Model/*/*.php",
	__DIR__ . "/../src/Model/*/*/*.php",
);

foreach($load as $path)
{
				$files = glob($path);
				foreach($files as $file)
				{
								$exp = explode("/../src/", $file);
								$basename = basename($file);
								$callName = str_replace(".php", "", $basename);
								$namespace = "ProjectRena\\" . str_replace(".php", "", str_replace("/", "\\", $exp[1]));

								if(stristr($file, "EVEApi"))
								{
												$ep = explode("/EVEApi/", $file);
												$ep = explode("/", $ep[1]);
												$callName = "EVE" . $ep[0] . $callName;
								}

								if(method_exists(new $namespace($app), "RunAsNew") || php_sapi_name() == "cli")
								{
												$app->container->set(/**
												 * @param $container
												 *
												 * @return mixed
												 */
													$callName, function ($container) use ($app, $namespace)
												{
																return new $namespace($app);
												});
								} else
								{
												// Load all the models and Libraries as singletons in Slim..
												$app->container->singleton(/**
												 * @param $container
												 *
												 * @return mixed
												 */
													$callName, function ($container) use ($app, $namespace)
												{
																return new $namespace($app);
												});
								}
				}
}