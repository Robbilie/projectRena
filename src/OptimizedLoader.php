<?php
$app->container->singleton("baseConfig", function($container) use ($app)
{
	return new \ProjectRena\Lib\baseConfig();
});

$app->container->singleton("Cache", function($container) use ($app)
{
		return new \ProjectRena\Lib\Cache($app);
});

$app->container->singleton("Db", function($container) use ($app)
{
		return new \ProjectRena\Lib\Db($app);
});

$app->container->singleton("Logging", function($container) use ($app)
{
		return new \ProjectRena\Lib\Logging($app);
});

$app->container->singleton("Pheal", function($container) use ($app)
{
		return new \ProjectRena\Lib\Pheal($app);
});

$app->container->singleton("PhealLogger", function($container) use ($app)
{
		return new \ProjectRena\Lib\PhealLogger($app);
});

$app->container->set("SessionHandler", function($container) use ($app)
{
		return new \ProjectRena\Lib\SessionHandler($app);
});

$app->container->singleton("StatsD", function($container) use ($app)
{
		return new \ProjectRena\Lib\StatsD($app);
});

$app->container->set("Timer", function($container) use ($app)
{
		return new \ProjectRena\Lib\Timer($app);
});

$app->container->singleton("baseConfig", function($container) use ($app)
{
		return new \ProjectRena\Lib\baseConfig($app);
});

$app->container->singleton("cURL", function($container) use ($app)
{
		return new \ProjectRena\Lib\cURL($app);
});

$app->container->set("out", function($container) use ($app)
{
		return new \ProjectRena\Lib\out($app);
});

$app->container->singleton("EVEOAuth", function($container) use ($app)
{
		return new \ProjectRena\Lib\OAuth\EVEOAuth($app);
});

$app->container->singleton("ApiKeyCharacters", function($container) use ($app)
{
		return new \ProjectRena\Model\ApiKeyCharacters($app);
});

$app->container->singleton("ApiKeys", function($container) use ($app)
{
		return new \ProjectRena\Model\ApiKeys($app);
});

$app->container->singleton("Config", function($container) use ($app)
{
		return new \ProjectRena\Model\Config($app);
});

$app->container->singleton("Groups", function($container) use ($app)
{
		return new \ProjectRena\Model\Groups($app);
});

$app->container->singleton("Paste", function($container) use ($app)
{
		return new \ProjectRena\Model\Paste($app);
});

$app->container->singleton("Search", function($container) use ($app)
{
		return new \ProjectRena\Model\Search($app);
});

$app->container->singleton("Storage", function($container) use ($app)
{
		return new \ProjectRena\Model\Storage($app);
});

$app->container->singleton("Users", function($container) use ($app)
{
		return new \ProjectRena\Model\Users($app);
});

$app->container->singleton("UsersGroups", function($container) use ($app)
{
		return new \ProjectRena\Model\UsersGroups($app);
});

$app->container->singleton("UsersLogins", function($container) use ($app)
{
		return new \ProjectRena\Model\UsersLogins($app);
});

$app->container->singleton("dgmAttributeCategories", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\dgmAttributeCategories($app);
});

$app->container->singleton("dgmAttributeTypes", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\dgmAttributeTypes($app);
});

$app->container->singleton("dgmEffects", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\dgmEffects($app);
});

$app->container->singleton("dgmTypeAttributes", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\dgmTypeAttributes($app);
});

$app->container->singleton("dgmTypeEffects", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\dgmTypeEffects($app);
});

$app->container->singleton("invFlags", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\invFlags($app);
});

$app->container->singleton("invGroups", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\invGroups($app);
});

$app->container->singleton("invTypes", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\invTypes($app);
});

$app->container->singleton("mapDenormalize", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\mapDenormalize($app);
});

$app->container->singleton("mapRegions", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\mapRegions($app);
});

$app->container->singleton("mapSolarSystems", function($container) use ($app)
{
		return new \ProjectRena\Model\CCP\mapSolarSystems($app);
});

$app->container->set("CoreAlliance", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreAlliance($app);
});

$app->container->set("CoreBase", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreBase($app);
});

$app->container->set("CoreCharacter", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreCharacter($app);
});

$app->container->set("CoreContainer", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreContainer($app);
});

$app->container->set("CoreControltower", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreControltower($app);
});

$app->container->set("CoreCorporation", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreCorporation($app);
});

$app->container->set("CoreFleet", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreFleet($app);
});

$app->container->set("CoreFleetParticipant", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreFleetParticipant($app);
});

$app->container->set("CoreGroup", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreGroup($app);
});

$app->container->set("CoreItem", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreItem($app);
});

$app->container->set("CoreItemType", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreItemType($app);
});

$app->container->set("CoreLocation", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreLocation($app);
});

$app->container->singleton("CoreManager", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreManager($app);
});

$app->container->set("CoreNotification", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreNotification($app);
});

$app->container->set("CorePermission", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CorePermission($app);
});

$app->container->set("CoreReactionModule", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreReactionModule($app);
});

$app->container->set("CoreStructure", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreStructure($app);
});

$app->container->set("CoreTimer", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreTimer($app);
});

$app->container->set("CoreUser", function($container) use ($app)
{
		return new \ProjectRena\Model\Core\CoreUser($app);
});

$app->container->singleton("alliances", function($container) use ($app)
{
		return new \ProjectRena\Model\EVE\alliances($app);
});

$app->container->singleton("characters", function($container) use ($app)
{
		return new \ProjectRena\Model\EVE\characters($app);
});

$app->container->singleton("corporations", function($container) use ($app)
{
		return new \ProjectRena\Model\EVE\corporations($app);
});

$app->container->singleton("killmails", function($container) use ($app)
{
		return new \ProjectRena\Model\EVE\killmails($app);
});

$app->container->singleton("participants", function($container) use ($app)
{
		return new \ProjectRena\Model\EVE\participants($app);
});

$app->container->singleton("EVEAPICallList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\API\CallList($app);
});

$app->container->singleton("EVEAccountAPIKeyInfo", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Account\APIKeyInfo($app);
});

$app->container->singleton("EVEAccountAccountStatus", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Account\AccountStatus($app);
});

$app->container->singleton("EVEAccountCharacters", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Account\Characters($app);
});

$app->container->singleton("EVECharacterAccountBalance", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\AccountBalance($app);
});

$app->container->singleton("EVECharacterAssetList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\AssetList($app);
});

$app->container->singleton("EVECharacterBlueprints", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Blueprints($app);
});

$app->container->singleton("EVECharacterCalendarEventAttendees", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\CalendarEventAttendees($app);
});

$app->container->singleton("EVECharacterCharacterSheet", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\CharacterSheet($app);
});

$app->container->singleton("EVECharacterContactList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\ContactList($app);
});

$app->container->singleton("EVECharacterContactNotifications", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\ContactNotifications($app);
});

$app->container->singleton("EVECharacterContractBids", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\ContractBids($app);
});

$app->container->singleton("EVECharacterContractItems", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\ContractItems($app);
});

$app->container->singleton("EVECharacterContracts", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Contracts($app);
});

$app->container->singleton("EVECharacterFacWarStats", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\FacWarStats($app);
});

$app->container->singleton("EVECharacterIndustryJobs", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\IndustryJobs($app);
});

$app->container->singleton("EVECharacterIndustryJobsHistory", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\IndustryJobsHistory($app);
});

$app->container->singleton("EVECharacterKillMails", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\KillMails($app);
});

$app->container->singleton("EVECharacterLocations", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Locations($app);
});

$app->container->singleton("EVECharacterMailBodies", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\MailBodies($app);
});

$app->container->singleton("EVECharacterMailMessages", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\MailMessages($app);
});

$app->container->singleton("EVECharacterMailingLists", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\MailingLists($app);
});

$app->container->singleton("EVECharacterMarketOrders", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\MarketOrders($app);
});

$app->container->singleton("EVECharacterMedals", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Medals($app);
});

$app->container->singleton("EVECharacterNotificationTexts", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\NotificationTexts($app);
});

$app->container->singleton("EVECharacterNotifications", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Notifications($app);
});

$app->container->singleton("EVECharacterPlanetaryColonies", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\PlanetaryColonies($app);
});

$app->container->singleton("EVECharacterPlanetaryLinks", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\PlanetaryLinks($app);
});

$app->container->singleton("EVECharacterPlanetaryPins", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\PlanetaryPins($app);
});

$app->container->singleton("EVECharacterPlanetaryRoutes", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\PlanetaryRoutes($app);
});

$app->container->singleton("EVECharacterResearch", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Research($app);
});

$app->container->singleton("EVECharacterSkillInTraining", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\SkillInTraining($app);
});

$app->container->singleton("EVECharacterSkillQueue", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\SkillQueue($app);
});

$app->container->singleton("EVECharacterStandings", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\Standings($app);
});

$app->container->singleton("EVECharacterUpcomingCalendarEvents", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\UpcomingCalendarEvents($app);
});

$app->container->singleton("EVECharacterWalletJournal", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\WalletJournal($app);
});

$app->container->singleton("EVECharacterWalletTransactions", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Character\WalletTransactions($app);
});

$app->container->singleton("EVECorporationAccountBalance", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\AccountBalance($app);
});

$app->container->singleton("EVECorporationAssetList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\AssetList($app);
});

$app->container->singleton("EVECorporationBlueprints", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Blueprints($app);
});

$app->container->singleton("EVECorporationContactList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\ContactList($app);
});

$app->container->singleton("EVECorporationContainerLog", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\ContainerLog($app);
});

$app->container->singleton("EVECorporationContractBids", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\ContractBids($app);
});

$app->container->singleton("EVECorporationContractItems", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\ContractItems($app);
});

$app->container->singleton("EVECorporationContracts", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Contracts($app);
});

$app->container->singleton("EVECorporationCorporationSheet", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\CorporationSheet($app);
});

$app->container->singleton("EVECorporationCustomsOffices", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\CustomsOffices($app);
});

$app->container->singleton("EVECorporationFacWarStats", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\FacWarStats($app);
});

$app->container->singleton("EVECorporationFacilities", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Facilities($app);
});

$app->container->singleton("EVECorporationIndustryJobs", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\IndustryJobs($app);
});

$app->container->singleton("EVECorporationIndustryJobsHistory", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\IndustryJobsHistory($app);
});

$app->container->singleton("EVECorporationKillMails", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\KillMails($app);
});

$app->container->singleton("EVECorporationLocations", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Locations($app);
});

$app->container->singleton("EVECorporationMarketOrders", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\MarketOrders($app);
});

$app->container->singleton("EVECorporationMedals", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Medals($app);
});

$app->container->singleton("EVECorporationMemberMedals", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\MemberMedals($app);
});

$app->container->singleton("EVECorporationMemberSecurity", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\MemberSecurity($app);
});

$app->container->singleton("EVECorporationMemberSecurityLog", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\MemberSecurityLog($app);
});

$app->container->singleton("EVECorporationMemberTracking", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\MemberTracking($app);
});

$app->container->singleton("EVECorporationOutpostList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\OutpostList($app);
});

$app->container->singleton("EVECorporationOutpostServiceDetail", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\OutpostServiceDetail($app);
});

$app->container->singleton("EVECorporationShareholders", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Shareholders($app);
});

$app->container->singleton("EVECorporationStandings", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Standings($app);
});

$app->container->singleton("EVECorporationStarbaseDetail", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\StarbaseDetail($app);
});

$app->container->singleton("EVECorporationStarbaseList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\StarbaseList($app);
});

$app->container->singleton("EVECorporationTitles", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\Titles($app);
});

$app->container->singleton("EVECorporationWalletJournal", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\WalletJournal($app);
});

$app->container->singleton("EVECorporationWalletTransactions", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Corporation\WalletTransactions($app);
});

$app->container->singleton("EVEEVEAllianceList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\AllianceList($app);
});

$app->container->singleton("EVEEVECharacterAffiliation", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\CharacterAffiliation($app);
});

$app->container->singleton("EVEEVECharacterID", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\CharacterID($app);
});

$app->container->singleton("EVEEVECharacterInfo", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\CharacterInfo($app);
});

$app->container->singleton("EVEEVECharacterName", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\CharacterName($app);
});

$app->container->singleton("EVEEVEConquerableStationList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\ConquerableStationList($app);
});

$app->container->singleton("EVEEVEErrorList", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\ErrorList($app);
});

$app->container->singleton("EVEEVEFacWarStats", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\FacWarStats($app);
});

$app->container->singleton("EVEEVEFacWarTopStats", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\FacWarTopStats($app);
});

$app->container->singleton("EVEEVERefTypes", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\RefTypes($app);
});

$app->container->singleton("EVEEVESkillTree", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\SkillTree($app);
});

$app->container->singleton("EVEEVETypeName", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\EVE\TypeName($app);
});

$app->container->singleton("EVEMapFacWarSystems", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Map\FacWarSystems($app);
});

$app->container->singleton("EVEMapJumps", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Map\Jumps($app);
});

$app->container->singleton("EVEMapKills", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Map\Kills($app);
});

$app->container->singleton("EVEMapSovereignty", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Map\Sovereignty($app);
});

$app->container->singleton("EVEServerServerStatus", function($container) use ($app)
{
		return new \ProjectRena\Model\EVEApi\Server\ServerStatus($app);
});
