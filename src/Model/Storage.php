<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Class Storage
 *
 * @package ProjectRena\Model
 */
class Storage
{
				/**
				 * @var RenaApp
				 */
				private $app;
				/**
				 * @var \ProjectRena\Lib\Db
				 */
				private $db;
				/**
				 * @var \ProjectRena\Lib\baseConfig
				 */
				private $config;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->db = $this->app->Db;
								$this->config = $this->app->baseConfig;
				}

				/**
				 * @param string $key
				 * @param null $default
				 *
				 * @return null
				 */
				function get($key, $default = null)
				{
								$value = $this->db->queryField("SELECT value FROM storage WHERE `key` = :key", "value", array(":key" => $key), 0);

								if(!$value) return $default;

								return $value;
				}

				/**
				 * @param string $key
				 * @param string $value
				 *
				 * @return bool|int|string
				 */
				function set($key, $value)
				{
								return $this->db->execute("REPLACE INTO storage (`key`, value) VALUES (:key, :value)", array(":key"   => $key,
								                                                                                             ":value" => $value,
									));
				}
}