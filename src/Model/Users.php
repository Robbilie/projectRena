<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Class Users
 *
 * @package ProjectRena\Model
 */
class Users
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
				 * @var \ProjectRena\Lib\Db
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
				 * @param $userID
				 *
				 * @return array
				 */
				public function getUserByID($userID)
				{
								return $this->db->queryRow("SELECT * FROM users WHERE id = :userID", array(":userID" => $userID));
				}

				/**
				 * @param $characterName
				 *
				 * @return array
				 */
				public function getUserByName($characterName)
				{
								return $this->db->queryRow("SELECT * FROM users WHERE characterName = :characterName", array(":characterName" => $characterName));
				}

				/**
				 * @param $characterName
				 * @param $characterID
				 * @param $characterOwnerHash
				 *
				 * @return int
				 */
				public function createUserWithCrest($characterName, $characterID, $characterOwnerHash)
				{
								$id = $this->db->queryField("SELECT id FROM users WHERE characterName = :characterName", "id", array(":characterName" => $characterName));
								if(!$id)
								{
												return $this->db->execute("INSERT INTO users (characterName, characterID, characterOwnerHash) VALUE (:characterName, :characterID, :characterOwnerHash)", array(
														":characterName"      => $characterName,
														":characterID"        => $characterID,
														":characterOwnerHash" => $characterOwnerHash,
													), true);
								}

								return $id;
				}

				/**
				 * @param $userID
				 * @param $hash
				 *
				 * @return bool|int|string
				 */
				public function setUserAutoLoginHash($userID, $hash)
				{
								return $this->db->execute("UPDATE users SET loginHash = :hash WHERE id = :userID", array(":hash"   => $hash,
								                                                                                         ":userID" => $userID,
									));
				}

				/**
				 * Tries to autologin the person
				 */
				public function tryAutologin()
				{
								$cookieName = $this->config->getConfig("name", "cookies");
								$cookieData = $this->app->getEncryptedCookie($cookieName, false);

								if(!empty($cookieData) && !isset($_SESSION["loggedIn"]))
								{
												$userData = $this->getUserDataByLoginHash($cookieData);
												if($userData)
												{
																$_SESSION["characterName"] = $userData["characterName"];
																$_SESSION["characterID"] = $userData["characterID"];
																$_SESSION["loggedIn"] = true;

																// Using $app to redirect causes a weird bug in slim, so use a header Location: instead
																header("Location: " . $this->app->request->getPath());
												}
								}
				}

				/**
				 * @param $hash
				 *
				 * @return array
				 */
				public function getUserDataByLoginHash($hash)
				{
								return $this->db->queryRow("SELECT * FROM users WHERE loginHash = :hash", array(":hash" => $hash));
				}
}