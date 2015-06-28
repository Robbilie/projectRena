<?php
// Cheatsheet: https://andreiabohner.files.wordpress.com/2014/06/slim.pdf
// Main route

// JSON
$app->get('/json/status/', function() use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getStatus();
});

$app->get('/json/apikey/:keyID/:vCode/', function($keyID, $vCode) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->submitAPIKey($keyID, $vCode);
});

$app->get('/json/characters/', function() use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCharacters();
});

$app->get('/json/character/:characterID/', function($characterID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCharacter($characterID);
});

$app->get('/json/character/switch/:characterID/', function($characterID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->switchCharacter($characterID);
});

$app->get('/json/character/delete/:characterID/', function($characterID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->removeCharacter($characterID);
});

$app->get('/json/character/:characterID/groups/', function($characterID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCharacterGroups($characterID);
});

$app->get('/json/structures/controltower/', function() use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getControltowers();
});

$app->get('/json/structures/controltower/:towerID/', function($towerID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getControltower($towerID);
});

$app->get('/json/groups/', function() use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getGroups();
});

$app->get('/json/group/:groupid/', function($groupID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getGroup($groupID);
});

$app->get('/json/group/:groupid/members/', function($groupID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getGroupMembers($groupID);
});

$app->get('/json/group/:groupid/remove/permission/:id/', function($groupid, $id) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->removePermissionFromGroup($groupid, $id);
});

$app->get('/json/group/:groupid/add/permission/:id/', function($groupid, $id) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->addPermissionToGroup($groupid, $id);
});

$app->get('/json/group/:groupid/remove/character/:id/', function($groupid, $id) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->removeCharacterFromGroup($groupid, $id);
});

$app->get('/json/group/:groupid/add/character/:id/', function($groupid, $id) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->addCharacterToGroup($groupid, $id);
});

$app->get('/json/group/create/:name/:scope/:private/', function($name, $scope, $private) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->createGroup($name, $scope, $private == "true");
});

$app->get('/json/corporation/:corporationID/', function($corporationID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCorporation($corporationID);
});

$app->get('/json/corporation/:corporationID/members/', function($corporationID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCorporationMembers($corporationID);
});

$app->get('/json/alliance/:allianceID/', function($allianceID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getAlliance($allianceID);
});

$app->get('/json/alliance/:allianceID/members/', function($allianceID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getAllianceMembers($allianceID);
});

$app->get('/json/alliance/:allianceID/corporations/', function($allianceID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getAllianceCorporations($allianceID);
});

$app->get('/json/permissions/:scope/', function($scope) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getPermissionsByScope($scope);
});

$app->get('/json/corporation/:corporationID/container/:containerID/', function($corporationID, $containerID) use ($app){
    (new \ProjectRena\Controller\JSONController($app))->getCorporationContents($corporationID, $containerID);
});

$app->get('/json/intel/system/', function() use ($app){
    (new \ProjectRena\Controller\IntelController($app))->getSystemIntel();
});

$app->post('/json/intel/system/', function () use ($app){
	(new \ProjectRena\Controller\IntelController($app))->setSystemIntel();
});

$app->get('/json/intel/system/:systemID/', function($systemID) use ($app){
    (new \ProjectRena\Controller\IntelController($app))->getSystemIntel($systemID);
});

$app->post('/json/intel/system/:systemID/', function ($systemID) use ($app){
	(new \ProjectRena\Controller\IntelController($app))->setSystemIntel($systemID);
});

$app->get('/json/intel/region/', function() use ($app){
    (new \ProjectRena\Controller\IntelController($app))->getRegionIntel();
});

$app->get('/json/intel/region/:regionID/', function($regionID) use ($app){
    (new \ProjectRena\Controller\IntelController($app))->getRegionIntel($regionID);
});

$app->get('/json/fleets/', function () use ($app){
	(new \ProjectRena\Controller\FleetsController($app))->getFleets();
});

$app->post('/json/fleets/create/', function () use ($app){
	(new \ProjectRena\Controller\FleetsController($app))->createFleet();
});

$app->get('/json/fleet/:fleetID/', function ($fleetID) use ($app){
	(new \ProjectRena\Controller\FleetsController($app))->getFleet($fleetID);
});

$app->get('/json/fleets/confirm/:hash/', function ($hash) use ($app){
	(new \ProjectRena\Controller\FleetsController($app))->confirmFleet($hash);
});


/*
 * Search
 */


$app->get('/json/systemnames/:name', function ($name) use ($app){
  (new \ProjectRena\Controller\SearchController($app))->findSystemNames($name);
});

$app->get('/json/characternames/:name', function ($name) use ($app){
  (new \ProjectRena\Controller\SearchController($app))->findCharacterNames($name);
});


/*
 * Notifications
 */


 $app->get('/json/notifications/', function() use ($app){
     (new \ProjectRena\Controller\NotificationsController($app))->getNotifications();
 });

 $app->get('/json/notifications/unread/', function() use ($app){
     (new \ProjectRena\Controller\NotificationsController($app))->getUnreadCount();
 });

$app->get('/json/notifications/templates/', function() use ($app){
   (new \ProjectRena\Controller\NotificationsController($app))->getTemplates();
});

$app->get('/json/notifications/:notificationID/', function($notificationID) use ($app){
    (new \ProjectRena\Controller\NotificationsController($app))->getNotification($notificationID);
});


 /*
  * Fetcher
  */


$app->get('/fetcher/postapifetch/', function () use ($app) {
  (new \ProjectRena\Controller\FetcherController($app))->postApiFetch();
});



/*
 * Content
 */


$app->get('/home/', function() use ($app){
    $app->render("/pages/home.twig");
});

$app->get('/profile/', function() use ($app){
    $app->render("/pages/charactersheet.twig");
});

$app->get('/profile/:characterID/', function($characterID) use ($app){
    $app->render("/pages/profile.twig");
});

$app->get('/notifications/', function() use ($app){
    $app->render("/pages/notifications.twig");
});

$app->get('/mails/', function() use ($app){
    $app->render("/pages/mails.twig");
});

$app->get('/intel/', function() use ($app){
    $app->render("/pages/intel.twig");
});

$app->get('/intel/:intel/', function($intel) use ($app){
    $app->render("/pages/intel.twig");
});

$app->get('/intel/:intel/:id/', function($intel, $id) use ($app){
    $app->render("/pages/intel.twig");
});

$app->get('/logistic/', function() use ($app){
    $app->render("/pages/logistic.twig");
});

$app->get('/corporation/', function() use ($app){
    $app->render("/pages/corporation.twig");
});

$app->get('/structures/', function() use ($app){
    $app->render("/pages/structures.twig", array("structure" => ""));
});

$app->get('/structures/:structure/', function($structure) use ($app){
    $app->render("/pages/structures.twig");
});

$app->get('/corporation/:corporationID/container/:containerID/', function($corporationID, $containerID) use ($app){
    $app->render("/pages/contents.twig");
});

$app->get('/assets/', function() use ($app){
    $app->render("/pages/assets.twig");
});

$app->get('/assets/:asset/', function($asset) use ($app){
    $app->render("/pages/assets.twig");
});

$app->get('/fittings/', function() use ($app){
    $app->render("/pages/fittings.twig");
});

$app->get('/members/', function() use ($app){
    $app->render("/pages/members.twig");
});

$app->get('/members/:member/', function($member) use ($app){
    $app->render("/pages/members.twig");
});

$app->get('/members/:member/:id/', function($member, $id) use ($app){
    $app->render("/pages/members.twig");
});

$app->get('/groups/', function() use ($app){
    $app->render("/pages/groups.twig");
});

$app->get('/group/:groupID/', function($groupID) use ($app){
    $app->render("/pages/group.twig");
});

$app->get('/settings/', function() use ($app){
    $app->render("/pages/settings.twig");
});

$app->get('/help/', function() use ($app){
    $app->render("/pages/help.twig");
});

$app->get('/about/', function() use ($app){
    $app->render("/pages/about.twig");
});

$app->get('/structures/controltower/:towerID/', function($towerID) use ($app){
    $app->render("/pages/controltower.twig");
});

$app->get('/map/region/:regionID/', function($regionID) use ($app){
    $app->response->headers->set('Content-Type', 'image/svg+xml');
    $svg = file_get_contents("http://evemaps.dotlan.net/svg/".str_replace(" ", "_", $app->mapRegions->getRegionNameByID($regionID)).".svg");
    $svg = explode('<g id="controls"', $svg)[0];
    $svg .= '<script>function init (e) {} window.onload = function() { if(parent.mapLoaded) parent.mapLoaded(); }</script></svg>';
    echo $svg;
});

$app->get('/fleets/', function() use ($app){
	$app->render("/pages/fleets.twig");
});

$app->get('/fleet/:fleetID/', function($fleetID) use ($app){
	$app->render("/pages/fleet.twig");
});

$app->get('/fleets/confirm/:hash/', function($hash) use ($app){
	$app->render("/pages/fleetsconfirm.twig");
});





$app->get('/', function () use ($app)
{
                (new \ProjectRena\Controller\IndexController($app))->index();
});

// Paste Page
$app->get("/paste/", function () use ($app)
{
                (new \ProjectRena\Controller\PasteController($app))->pastePage();
});
$app->post("/paste/", function () use ($app)
{
                (new \ProjectRena\Controller\PasteController($app))->postPaste();
});
$app->get("/paste/:hash/", function ($hash) use ($app)
{
                (new \ProjectRena\Controller\PasteController($app))->showPaste($hash);
});

// Login
$app->get('/login/eve/', function () use ($app)
{
                (new \ProjectRena\Controller\LoginController($app))->loginEVE();
});

// Logout
$app->get('/logout/', function () use ($app)
{
                $sessionData = $_SESSION;
                foreach($sessionData as $key => $val)
                {
                                unset($_SESSION[$key]);
                }

                $cookieName = $app->baseConfig->getConfig("name", "cookies");
                $cookieSSL = $app->baseConfig->getConfig("ssl", "cookies");
                $app->deleteCookie($cookieName, "/", $app->request->getHost(), $cookieSSL, true);
                $app->redirect("/");
});
