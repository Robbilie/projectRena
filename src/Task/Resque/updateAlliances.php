<?php

namespace ProjectRena\Task\Resque;

/**
	* Class updateCharacter
	*
	* @package ProjectRena\Task\Resque
	*/
class updateAlliances
{
				protected $app;

				/**
				 *
				 */
				public function setUp()
				{
								$this->app = \ProjectRena\RenaApp::getInstance();
				}

				/**
				 *
				 */
				public function perform()
				{
								if($this->app->Storage->get("Api904") >= date("Y-m-d H:i:s"))
												return;

								$this->app->StatsD->increment("ccpRequests");
								$data = $this->app->EVEEVEAllianceList->getData();
								if(isset($data["result"]["alliances"]))
								{
												foreach($data["result"]["alliances"] as $alliance)
												{
																// Update all the corporations in the alliance.. maybe we missed one?
																foreach($alliance["memberCorporations"] as $corporation)
																				\Resque::enqueue("default", "\\ProjectRena\\Task\\Resque\\updateCorporation", array("corporationID" => $corporation["corporationID"]));

																$allianceID = $alliance["allianceID"];
																$allianceName = $alliance["name"];
																$allianceTicker = $alliance["shortName"];
																$memberCount = $alliance["memberCount"];
																$executorCorporationID = $alliance["executorCorpID"];
																$information = json_decode($this->app->cURL->getData("https://public-crest.eveonline.com/alliances/{$allianceID}/"), true)["description"];
																$this->app->alliances->updateAllianceDetails($allianceID, $allianceName, $allianceTicker, $memberCount, $executorCorporationID, $information);
																$this->app->alliances->setLastUpdated($allianceID, date("Y-m-d H:i:s"));
												}
								}
				}

				/**
				 *
				 */
				public function tearDown()
				{
								$this->app = NULL;
				}
}
