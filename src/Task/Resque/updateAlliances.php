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
								$this->app->StatsD->increment("ccpRequests");
								$data = $this->app->EVEEVEAllianceList->getData();
								if(isset($data["result"]["alliances"]))
								{
												foreach($data["result"]["alliances"] as $alliance)
												{
																$allianceID = $alliance["allianceID"];
																$allianceName = $alliance["name"];
																$allianceTicker = $alliance["shortName"];
																$memberCount = $alliance["memberCount"];
																$executorCorporationID = $alliance["executorCorpID"];
																$information = json_decode($this->app->cURL->getData("http://public-crest.eveonline.com/alliances/{$allianceID}/"), true)["description"];
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
