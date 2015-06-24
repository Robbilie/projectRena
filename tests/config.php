<?php
// set timezone for timestamps etc
date_default_timezone_set('UTC');
// Init the config array
$config = array();
// Site
$config["site"] = array(
    "debug"     => true,
    "userAgent" => null, // Use pre-defined user agents
);
// CCP
$config["ccp"] = array(
    "apiServer"   => "https://api.eveonline.com/",
    "imageServer" => "https://image.eveonline.com/",
);
// Database
$config["database"] = array(
    "host"             => "localhost",
    "username"         => "root",
    "password"         => "",
    "name"             => "rena",
    "emulatePrepares"  => true,
    "useBufferedQuery" => true,
);
// CREST SSO
$config["crestsso"] = array(
    "clientID"  => "",
    "secretKey" => "",
    "callBack"  => "/login/eve/",
);
// Cache
$config["redis"] = array(
    "host" => "127.0.0.1",
    "port" => 6379,
);
// Logging
$config["logging"] = array(
    "logFile"  => __DIR__ . "/../logs/app.log",
    "logLevel" => 100
    // debug is 100, 200 is info, 250 is notice, 300 is warning, 400 is error, 500 is critical, 550 is alert, 600 is emergency
);
// Cookies
$config["cookies"] = array(
    "name"   => "rena",
    "ssl"    => true,
    "time"   => (3600 * 24 * 30),
    "secret" => "SOMETHINGsuperSECRET",
);
// Slim
$config["slim"] = array(
    "mode"               => $config["site"]["debug"] ? "development" : "production",
    "debug"              => $config["site"]["debug"],
    "cookies.secret_key" => $config["cookies"]["secret"],
    "templates.path"     => __DIR__ . "/../view/",
);
// Twig
$config["twig"] = array(
    "charset"          => "utf-8",
    "debug"            => $config["site"]["debug"],
    "cache"            => __DIR__ . "/../cache/templates/",
    "auto_reload"      => true,
    "strict_variables" => false,
    "autoescape"       => true,
);