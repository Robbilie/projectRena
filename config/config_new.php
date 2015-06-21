<?php

// set timezone for timestamps etc
date_default_timezone_set('UTC');

// Init the config array
$config = array();

// Site
$config['site'] = array(
	'debug'     => true,
	'userAgent' => null, // Use pre-defined user agents

);

// CCP
$config['ccp'] = array(
	'apiServer'   => 'https://api.eveonline.com/',
	'imageServer' => 'https://image.eveonline.com/',
);

// Database
$config['database'] = array(
	'host'             => '',
	'username'         => '',
	'password'         => '',
	'name'             => '',
	'emulatePrepares'  => true,
	'useBufferedQuery' => true,
);

// CREST SSO
$config['crestsso'] = array(
	'clientID'  => '',
	'secretKey' => '',
	'callBack'  => '/login/eve/',
);

// Cache
$config['redis'] = array(
	'host' => '127.0.0.1',
	'port' => 6379,
);

// Logging
$config['logging'] = array(
	'logFile' => __DIR__ . '/../logs/app.log',
);

// Cookies
$config['cookies'] = array(
	'name'   => 'rena',
	'ssl'    => true,
	'time'   => (3600 * 24 * 30),
	'secret' => 'SOMETHINGsuperSECRET',
);

// Slim
$config['slim'] = array(
	'mode'               => $config['site']['debug'] ? 'development' : 'production',
	'debug'              => $config['site']['debug'],
	'cookies.secret_key' => $config['cookies']['secret'],
	'templates.path'     => __DIR__ . '/../view/',
);

// Twig
$config['twig'] = array(
	'charset'          => 'utf-8',
	'debug'            => $config['site']['debug'],
	'cache'            => __DIR__ . '/../cache/templates/',
	'auto_reload'      => true,
	'strict_variables' => false,
	'autoescape'       => true,
);
