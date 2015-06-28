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
				 * @return boolean
				 */
    public function updateAPIKey($apiKey, $vCode, $userID = null)
				{
								return (bool) $this->db->execute('INSERT INTO apiKeys (keyID, vCode, userID) VALUES (:keyID, :vCode, :userID) ON DUPLICATE KEY UPDATE keyID = :keyID, vCode = :vCode, userID = :userID', array(':keyID'  => $apiKey,
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
					* @param $keyID
					*
					* @return null
					*/
    public function getVCodeByKeyID($keyID)
				{
								return $this->db->queryField("SELECT vCode FROM apiKeys WHERE keyID = :keyID", "vCode", array(":keyID" => $keyID));
				}

				/**
				 * @param $apiKey
				 *
				 * @return boolean
				 */
				public function deleteAPIKey($apiKey)
				{
								return (bool) $this->db->execute('DELETE FROM apiKeys WHERE keyID = :keyID', array(':keyID' => $apiKey));
				}

				/**
					* Sets last validation to 5 minutes into the future pr. default
					*
					* @param $keyID
					* @param int $lastValidated
					*
					* @return bool|int|string
					*/
    public function updateLastValidated($keyID, $lastValidated = 0)
				{
								if(!$lastValidated)
												$lastValidated = date("Y-m-d H:i:s", time() + 600);
								if($lastValidated)
												$lastValidated = date("Y-m-d H:i:s", time() + $lastValidated);

								return $this->db->execute("UPDATE apiKeys SET lastValidation = :lastValidation WHERE keyID = :keyID", array(":lastValidation" => $lastValidated, ":keyID" => $keyID));
				}

				/**
					* @param $keyID
					* @param $errorCode
					*
					* @return bool|int|string
					*/
    public function setErrorCode($keyID, $errorCode)
				{
								return $this->db->execute("UPDATE apiKeys SET errorCode = :errorCode WHERE keyID = :keyID", array(":errorCode" => $errorCode, ":keyID" => $keyID));
				}
}