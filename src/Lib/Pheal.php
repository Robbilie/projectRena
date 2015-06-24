<?php
namespace ProjectRena\Lib;

use Pheal\Core\Config;
use ProjectRena\RenaApp;

/**
 * Class Pheal
 *
 * @package ProjectRena\Lib
 */
class Pheal
{
				/**
				 * @var
				 */
				protected $app;

				/**
					* @param RenaApp $app
					*/
				function __construct(RenaApp $app)
				{
								Config::getInstance()->http_method = "curl";
								Config::getInstance()->http_user_agent = $app->baseConfig->getConfig("userAgent", "site", "API DataGetter from projectRena (karbowiak@gmail.com)");
								Config::getInstance()->http_post = false;
								Config::getInstance()->http_keepalive = 10; // 10 seconds keep alive
								Config::getInstance()->http_timeout = 30;
								Config::getInstance()->cache = new \Pheal\Cache\RedisStorage(array(
									"host"       => $app->baseConfig->getConfig("host", "redis", "127.0.0.1"),
									"port"       => $app->baseConfig->getConfig("port", "redis", 6379),
									"persistent" => false,
									"auth"       => null,
									"prefix"     => "Pheal",
								));
								Config::getInstance()->log = new \ProjectRena\Lib\PhealLogger();
								Config::getInstance()->api_customkeys = true;
								Config::getInstance()->api_base = $app->baseConfig->getConfig("apiServer", "ccp", "https://api.eveonline.com/");
				}

				/**
				 *
				 */
				function RunAsNew()
				{
				}

				/**
				 * @return \Pheal\Pheal
				 */
				public function Pheal()
				{
								return new \Pheal\Pheal();
				}
}