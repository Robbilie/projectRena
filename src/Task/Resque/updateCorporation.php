<?php

namespace ProjectRena\Task\Resque;

/**
	* Class updateCharacter
	*
	* @package ProjectRena\Task\Resque
	*/
class updateCorporation
{
				/**
					* @var
					*/
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
								$this->app->StatsD->increment("corporationsUpdated");
								$corporationID = $this->args["corporationID"];

								// Get the character affiliate data
								$data = $this->app->EVECorporationCorporationSheet->getData(null, null, $corporationID);
								$this->app->corporations->updateCorporationDetails(
												$corporationID,
												$data["result"]["allianceID"],
												$data["result"]["corporationName"],
												$data["result"]["ceoID"],
												$data["result"]["ticker"],
												$data["result"]["memberCount"],
												json_encode($data["result"]));
								$this->app->corporations->setLastUpdated($corporationID, date("Y-m-d H:i:s"));
				}

				/**
				 *
				 */
				public function tearDown()
				{
								$this->app = NULL;
				}
}
