<?php
// change to the dir Rena is in
chdir(__DIR__);

// Error display
ini_set("display_errors", 0);
error_reporting(E_ALL);

// Load the autoloader
if(file_exists(__DIR__ . "/../vendor/autoload.php"))
{
    require_once __DIR__ . "/../vendor/autoload.php";
} else
{
    throw new Exception("vendor/autoload.php not found, make sure you run composer install");
}

// Require the config
if(file_exists(__DIR__ . "/../config/config.php"))
{
    require_once __DIR__ . "/../config/config.php";
} else
{
    throw new Exception("config.php not found (you might wanna start by copying config_new.php)");
}

// Init Slim
$app = new \ProjectRena\RenaApp($config["slim"]);

// Mock $app
$app->environment = Slim\Environment::mock();

// Load the lib/Model loader
if(file_exists(__DIR__ . "/../src/Loader.php"))
{
    require_once __DIR__ . "/../src/Loader.php";
} else
{
    throw new Exception("Loader.php could not be found");
}