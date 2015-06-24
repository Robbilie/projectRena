<?php

namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Class Config.
 */
class Config
{
				private $app;
				private $db;

				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->db = $app->Db;
				}

				/**
				 * @param $key
				 *
				 * @return mixed
				 */
				public function get($key)
				{
								$dbResult = $this->db->queryField('SELECT value FROM configuration WHERE `key` = :key', 'value', array(':key' => $key));

								return $dbResult;
				}

				/**
				 * @param $key
				 * @param $value
				 *
				 * @return mixed
				 */
				public function set($key, $value)
				{
								return $this->db->execute('INSERT INTO configuration (`key`, value) VALUES (:key, :value)', array(':key'   => $key,
								                                                                                                  ':value' => $value,
									));
				}
}