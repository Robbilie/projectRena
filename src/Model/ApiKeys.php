<?php

namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Class ApiKeys.
 */
class ApiKeys
{
				/**
				 * @var RenaApp
				 */
				protected $app;
				/**
				 * @var \ProjectRena\Lib\Db
				 */
				protected $db;

				/**
				 * @param RenaApp $app
				 */
				function __construct(RenaApp $app)
				{
								$this->app = $app;
								$this->db = $app->Db;
				}


				/**
				 * @param $apiKey
				 * @param $vCode
				 * @param null $userID
				 *
				 * @return bool|int|string
				 */
    public function addAPIKey($apiKey, $vCode, $userID = null)
				{
								return $this->db->execute('INSERT INTO apiKeys (keyID, vCode, userID) VALUES (:keyID, :vCode, :userID)', array(':keyID'  => $apiKey,
								                                                                                                               ':vCode'  => $vCode,
								                                                                                                               ':userID' => $userID,
									));
				}

				/**
				 * @param $apiKey
				 *
				 * @return array
				 */
				public function getAPIKey($apiKey)
				{
								return $this->db->queryRow('SELECT * FROM apiKeys WHERE keyID = :keyID', array(':keyID' => $apiKey));
				}

				/**
				 * @param $apiKey
				 * @param $vCode
				 * @param $userID
				 *
				 * @return bool|int|string
				 */
				public function updateAPIKey($apiKey, $vCode, $userID)
				{
								return $this->db->execute('INSERT INTO apiKeys (keyID, vCode, userID) VALUES (:keyID, :vCode, :userID) ON DUPLICATE UPDATE keyID = :keyID, vCode = :vCode, userID = :userID', array(':keyID'  => $apiKey,
								                                                                                                                                                                                    ':vCode'  => $vCode,
								                                                                                                                                                                                    ':userID' => $userID,
									));
				}

				/**
				 * @param $apiKey
				 *
				 * @return bool|int|string
				 */
				public function deleteAPIKey($apiKey)
				{
								return $this->db->execute('DELETE FROM apiKeys WHERE keyID = :keyID', array(':keyID' => $apiKey));
				}
}