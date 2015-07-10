<?php

namespace ProjectRena;

use Slim\Slim;
use ProjectRena\Lib\Cache;
use ProjectRena\Lib\Db;
use ProjectRena\Lib\Logging;
use ProjectRena\Lib\Pheal;
use ProjectRena\Lib\PhealLogger;
use ProjectRena\Lib\SessionHandler;
use ProjectRena\Lib\StatsD;
use ProjectRena\Lib\Timer;
use ProjectRena\Lib\baseConfig;
use ProjectRena\Lib\cURL;
use ProjectRena\Lib\out;
use ProjectRena\Lib\OAuth\EVEOAuth;
use ProjectRena\Model\ApiKeyCharacters;
use ProjectRena\Model\ApiKeys;
use ProjectRena\Model\Config;
use ProjectRena\Model\Groups;
use ProjectRena\Model\Paste;
use ProjectRena\Model\Storage;
use ProjectRena\Model\Users;
use ProjectRena\Model\UsersGroups;
use ProjectRena\Model\UsersLogins;
use ProjectRena\Model\CCP\dgmAttributeCategories;
use ProjectRena\Model\CCP\dgmAttributeTypes;
use ProjectRena\Model\CCP\dgmEffects;
use ProjectRena\Model\CCP\dgmTypeAttributes;
use ProjectRena\Model\CCP\dgmTypeEffects;
use ProjectRena\Model\CCP\invFlags;
use ProjectRena\Model\CCP\invGroups;
use ProjectRena\Model\CCP\invTypes;
use ProjectRena\Model\CCP\mapDenormalize;
use ProjectRena\Model\CCP\mapRegions;
use ProjectRena\Model\CCP\mapSolarSystems;
use ProjectRena\Model\EVE\alliances;
use ProjectRena\Model\EVE\characters;
use ProjectRena\Model\EVE\corporations;
use ProjectRena\Model\EVE\killmails;
use ProjectRena\Model\EVE\participants;
use ProjectRena\Model\EVEApi\API\CallList as EVEAPICallList;
use ProjectRena\Model\EVEApi\Account\APIKeyInfo as EVEAccountAPIKeyInfo;
use ProjectRena\Model\EVEApi\Account\AccountStatus as EVEAccountAccountStatus;
use ProjectRena\Model\EVEApi\Account\Characters as EVEAccountCharacters;
use ProjectRena\Model\EVEApi\Character\AccountBalance as EVECharacterAccountBalance;
use ProjectRena\Model\EVEApi\Character\AssetList as EVECharacterAssetList;
use ProjectRena\Model\EVEApi\Character\Blueprints as EVECharacterBlueprints;
use ProjectRena\Model\EVEApi\Character\CalendarEventAttendees as EVECharacterCalendarEventAttendees;
use ProjectRena\Model\EVEApi\Character\CharacterSheet as EVECharacterCharacterSheet;
use ProjectRena\Model\EVEApi\Character\ContactList as EVECharacterContactList;
use ProjectRena\Model\EVEApi\Character\ContactNotifications as EVECharacterContactNotifications;
use ProjectRena\Model\EVEApi\Character\ContractBids as EVECharacterContractBids;
use ProjectRena\Model\EVEApi\Character\ContractItems as EVECharacterContractItems;
use ProjectRena\Model\EVEApi\Character\Contracts as EVECharacterContracts;
use ProjectRena\Model\EVEApi\Character\FacWarStats as EVECharacterFacWarStats;
use ProjectRena\Model\EVEApi\Character\IndustryJobs as EVECharacterIndustryJobs;
use ProjectRena\Model\EVEApi\Character\IndustryJobsHistory as EVECharacterIndustryJobsHistory;
use ProjectRena\Model\EVEApi\Character\KillMails as EVECharacterKillMails;
use ProjectRena\Model\EVEApi\Character\Locations as EVECharacterLocations;
use ProjectRena\Model\EVEApi\Character\MailBodies as EVECharacterMailBodies;
use ProjectRena\Model\EVEApi\Character\MailMessages as EVECharacterMailMessages;
use ProjectRena\Model\EVEApi\Character\MailingLists as EVECharacterMailingLists;
use ProjectRena\Model\EVEApi\Character\MarketOrders as EVECharacterMarketOrders;
use ProjectRena\Model\EVEApi\Character\Medals as EVECharacterMedals;
use ProjectRena\Model\EVEApi\Character\NotificationTexts as EVECharacterNotificationTexts;
use ProjectRena\Model\EVEApi\Character\Notifications as EVECharacterNotifications;
use ProjectRena\Model\EVEApi\Character\PlanetaryColonies as EVECharacterPlanetaryColonies;
use ProjectRena\Model\EVEApi\Character\PlanetaryLinks as EVECharacterPlanetaryLinks;
use ProjectRena\Model\EVEApi\Character\PlanetaryPins as EVECharacterPlanetaryPins;
use ProjectRena\Model\EVEApi\Character\PlanetaryRoutes as EVECharacterPlanetaryRoutes;
use ProjectRena\Model\EVEApi\Character\Research as EVECharacterResearch;
use ProjectRena\Model\EVEApi\Character\SkillInTraining as EVECharacterSkillInTraining;
use ProjectRena\Model\EVEApi\Character\SkillQueue as EVECharacterSkillQueue;
use ProjectRena\Model\EVEApi\Character\Standings as EVECharacterStandings;
use ProjectRena\Model\EVEApi\Character\UpcomingCalendarEvents as EVECharacterUpcomingCalendarEvents;
use ProjectRena\Model\EVEApi\Character\WalletJournal as EVECharacterWalletJournal;
use ProjectRena\Model\EVEApi\Character\WalletTransactions as EVECharacterWalletTransactions;
use ProjectRena\Model\EVEApi\Corporation\AccountBalance as EVECorporationAccountBalance;
use ProjectRena\Model\EVEApi\Corporation\AssetList as EVECorporationAssetList;
use ProjectRena\Model\EVEApi\Corporation\Blueprints as EVECorporationBlueprints;
use ProjectRena\Model\EVEApi\Corporation\ContactList as EVECorporationContactList;
use ProjectRena\Model\EVEApi\Corporation\ContainerLog as EVECorporationContainerLog;
use ProjectRena\Model\EVEApi\Corporation\ContractBids as EVECorporationContractBids;
use ProjectRena\Model\EVEApi\Corporation\ContractItems as EVECorporationContractItems;
use ProjectRena\Model\EVEApi\Corporation\Contracts as EVECorporationContracts;
use ProjectRena\Model\EVEApi\Corporation\CorporationSheet as EVECorporationCorporationSheet;
use ProjectRena\Model\EVEApi\Corporation\CustomsOffices as EVECorporationCustomsOffices;
use ProjectRena\Model\EVEApi\Corporation\FacWarStats as EVECorporationFacWarStats;
use ProjectRena\Model\EVEApi\Corporation\Facilities as EVECorporationFacilities;
use ProjectRena\Model\EVEApi\Corporation\IndustryJobs as EVECorporationIndustryJobs;
use ProjectRena\Model\EVEApi\Corporation\IndustryJobsHistory as EVECorporationIndustryJobsHistory;
use ProjectRena\Model\EVEApi\Corporation\KillMails as EVECorporationKillMails;
use ProjectRena\Model\EVEApi\Corporation\Locations as EVECorporationLocations;
use ProjectRena\Model\EVEApi\Corporation\MarketOrders as EVECorporationMarketOrders;
use ProjectRena\Model\EVEApi\Corporation\Medals as EVECorporationMedals;
use ProjectRena\Model\EVEApi\Corporation\MemberMedals as EVECorporationMemberMedals;
use ProjectRena\Model\EVEApi\Corporation\MemberSecurity as EVECorporationMemberSecurity;
use ProjectRena\Model\EVEApi\Corporation\MemberSecurityLog as EVECorporationMemberSecurityLog;
use ProjectRena\Model\EVEApi\Corporation\MemberTracking as EVECorporationMemberTracking;
use ProjectRena\Model\EVEApi\Corporation\OutpostList as EVECorporationOutpostList;
use ProjectRena\Model\EVEApi\Corporation\OutpostServiceDetail as EVECorporationOutpostServiceDetail;
use ProjectRena\Model\EVEApi\Corporation\Shareholders as EVECorporationShareholders;
use ProjectRena\Model\EVEApi\Corporation\Standings as EVECorporationStandings;
use ProjectRena\Model\EVEApi\Corporation\StarbaseDetail as EVECorporationStarbaseDetail;
use ProjectRena\Model\EVEApi\Corporation\StarbaseList as EVECorporationStarbaseList;
use ProjectRena\Model\EVEApi\Corporation\Titles as EVECorporationTitles;
use ProjectRena\Model\EVEApi\Corporation\WalletJournal as EVECorporationWalletJournal;
use ProjectRena\Model\EVEApi\Corporation\WalletTransactions as EVECorporationWalletTransactions;
use ProjectRena\Model\EVEApi\EVE\AllianceList as EVEEVEAllianceList;
use ProjectRena\Model\EVEApi\EVE\CharacterAffiliation as EVEEVECharacterAffiliation;
use ProjectRena\Model\EVEApi\EVE\CharacterID as EVEEVECharacterID;
use ProjectRena\Model\EVEApi\EVE\CharacterInfo as EVEEVECharacterInfo;
use ProjectRena\Model\EVEApi\EVE\CharacterName as EVEEVECharacterName;
use ProjectRena\Model\EVEApi\EVE\ConquerableStationList as EVEEVEConquerableStationList;
use ProjectRena\Model\EVEApi\EVE\ErrorList as EVEEVEErrorList;
use ProjectRena\Model\EVEApi\EVE\FacWarStats as EVEEVEFacWarStats;
use ProjectRena\Model\EVEApi\EVE\FacWarTopStats as EVEEVEFacWarTopStats;
use ProjectRena\Model\EVEApi\EVE\RefTypes as EVEEVERefTypes;
use ProjectRena\Model\EVEApi\EVE\SkillTree as EVEEVESkillTree;
use ProjectRena\Model\EVEApi\EVE\TypeName as EVEEVETypeName;
use ProjectRena\Model\EVEApi\Map\FacWarSystems as EVEMapFacWarSystems;
use ProjectRena\Model\EVEApi\Map\Jumps as EVEMapJumps;
use ProjectRena\Model\EVEApi\Map\Kills as EVEMapKills;
use ProjectRena\Model\EVEApi\Map\Sovereignty as EVEMapSovereignty;
use ProjectRena\Model\EVEApi\Server\ServerStatus as EVEServerServerStatus;

/**
 * @property Cache Cache
 * @property Db Db
 * @property Logging Logging
 * @property Pheal Pheal
 * @property PhealLogger PhealLogger
 * @property SessionHandler SessionHandler
 * @property StatsD StatsD
 * @property Timer Timer
 * @property baseConfig baseConfig
 * @property cURL cURL
 * @property out out
 * @property EVEOAuth EVEOAuth
 * @property ApiKeyCharacters ApiKeyCharacters
 * @property ApiKeys ApiKeys
 * @property Config Config
 * @property Groups Groups
 * @property Paste Paste
 * @property Storage Storage
 * @property Users Users
 * @property UsersGroups UsersGroups
 * @property UsersLogins UsersLogins
 * @property dgmAttributeCategories dgmAttributeCategories
 * @property dgmAttributeTypes dgmAttributeTypes
 * @property dgmEffects dgmEffects
 * @property dgmTypeAttributes dgmTypeAttributes
 * @property dgmTypeEffects dgmTypeEffects
 * @property invFlags invFlags
 * @property invGroups invGroups
 * @property invTypes invTypes
 * @property mapDenormalize mapDenormalize
 * @property mapRegions mapRegions
 * @property mapSolarSystems mapSolarSystems
 * @property alliances alliances
 * @property characters characters
 * @property corporations corporations
 * @property killmails killmails
 * @property participants participants
 * @property EVEAPICallList EVEAPICallList
 * @property EVEAccountAPIKeyInfo EVEAccountAPIKeyInfo
 * @property EVEAccountAccountStatus EVEAccountAccountStatus
 * @property EVEAccountCharacters EVEAccountCharacters
 * @property EVECharacterAccountBalance EVECharacterAccountBalance
 * @property EVECharacterAssetList EVECharacterAssetList
 * @property EVECharacterBlueprints EVECharacterBlueprints
 * @property EVECharacterCalendarEventAttendees EVECharacterCalendarEventAttendees
 * @property EVECharacterCharacterSheet EVECharacterCharacterSheet
 * @property EVECharacterContactList EVECharacterContactList
 * @property EVECharacterContactNotifications EVECharacterContactNotifications
 * @property EVECharacterContractBids EVECharacterContractBids
 * @property EVECharacterContractItems EVECharacterContractItems
 * @property EVECharacterContracts EVECharacterContracts
 * @property EVECharacterFacWarStats EVECharacterFacWarStats
 * @property EVECharacterIndustryJobs EVECharacterIndustryJobs
 * @property EVECharacterIndustryJobsHistory EVECharacterIndustryJobsHistory
 * @property EVECharacterKillMails EVECharacterKillMails
 * @property EVECharacterLocations EVECharacterLocations
 * @property EVECharacterMailBodies EVECharacterMailBodies
 * @property EVECharacterMailMessages EVECharacterMailMessages
 * @property EVECharacterMailingLists EVECharacterMailingLists
 * @property EVECharacterMarketOrders EVECharacterMarketOrders
 * @property EVECharacterMedals EVECharacterMedals
 * @property EVECharacterNotificationTexts EVECharacterNotificationTexts
 * @property EVECharacterNotifications EVECharacterNotifications
 * @property EVECharacterPlanetaryColonies EVECharacterPlanetaryColonies
 * @property EVECharacterPlanetaryLinks EVECharacterPlanetaryLinks
 * @property EVECharacterPlanetaryPins EVECharacterPlanetaryPins
 * @property EVECharacterPlanetaryRoutes EVECharacterPlanetaryRoutes
 * @property EVECharacterResearch EVECharacterResearch
 * @property EVECharacterSkillInTraining EVECharacterSkillInTraining
 * @property EVECharacterSkillQueue EVECharacterSkillQueue
 * @property EVECharacterStandings EVECharacterStandings
 * @property EVECharacterUpcomingCalendarEvents EVECharacterUpcomingCalendarEvents
 * @property EVECharacterWalletJournal EVECharacterWalletJournal
 * @property EVECharacterWalletTransactions EVECharacterWalletTransactions
 * @property EVECorporationAccountBalance EVECorporationAccountBalance
 * @property EVECorporationAssetList EVECorporationAssetList
 * @property EVECorporationBlueprints EVECorporationBlueprints
 * @property EVECorporationContactList EVECorporationContactList
 * @property EVECorporationContainerLog EVECorporationContainerLog
 * @property EVECorporationContractBids EVECorporationContractBids
 * @property EVECorporationContractItems EVECorporationContractItems
 * @property EVECorporationContracts EVECorporationContracts
 * @property EVECorporationCorporationSheet EVECorporationCorporationSheet
 * @property EVECorporationCustomsOffices EVECorporationCustomsOffices
 * @property EVECorporationFacWarStats EVECorporationFacWarStats
 * @property EVECorporationFacilities EVECorporationFacilities
 * @property EVECorporationIndustryJobs EVECorporationIndustryJobs
 * @property EVECorporationIndustryJobsHistory EVECorporationIndustryJobsHistory
 * @property EVECorporationKillMails EVECorporationKillMails
 * @property EVECorporationLocations EVECorporationLocations
 * @property EVECorporationMarketOrders EVECorporationMarketOrders
 * @property EVECorporationMedals EVECorporationMedals
 * @property EVECorporationMemberMedals EVECorporationMemberMedals
 * @property EVECorporationMemberSecurity EVECorporationMemberSecurity
 * @property EVECorporationMemberSecurityLog EVECorporationMemberSecurityLog
 * @property EVECorporationMemberTracking EVECorporationMemberTracking
 * @property EVECorporationOutpostList EVECorporationOutpostList
 * @property EVECorporationOutpostServiceDetail EVECorporationOutpostServiceDetail
 * @property EVECorporationShareholders EVECorporationShareholders
 * @property EVECorporationStandings EVECorporationStandings
 * @property EVECorporationStarbaseDetail EVECorporationStarbaseDetail
 * @property EVECorporationStarbaseList EVECorporationStarbaseList
 * @property EVECorporationTitles EVECorporationTitles
 * @property EVECorporationWalletJournal EVECorporationWalletJournal
 * @property EVECorporationWalletTransactions EVECorporationWalletTransactions
 * @property EVEEVEAllianceList EVEEVEAllianceList
 * @property EVEEVECharacterAffiliation EVEEVECharacterAffiliation
 * @property EVEEVECharacterID EVEEVECharacterID
 * @property EVEEVECharacterInfo EVEEVECharacterInfo
 * @property EVEEVECharacterName EVEEVECharacterName
 * @property EVEEVEConquerableStationList EVEEVEConquerableStationList
 * @property EVEEVEErrorList EVEEVEErrorList
 * @property EVEEVEFacWarStats EVEEVEFacWarStats
 * @property EVEEVEFacWarTopStats EVEEVEFacWarTopStats
 * @property EVEEVERefTypes EVEEVERefTypes
 * @property EVEEVESkillTree EVEEVESkillTree
 * @property EVEEVETypeName EVEEVETypeName
 * @property EVEMapFacWarSystems EVEMapFacWarSystems
 * @property EVEMapJumps EVEMapJumps
 * @property EVEMapKills EVEMapKills
 * @property EVEMapSovereignty EVEMapSovereignty
 * @property EVEServerServerStatus EVEServerServerStatus
 */

class RenaApp extends Slim
{
}