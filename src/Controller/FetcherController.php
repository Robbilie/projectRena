<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class FetcherController
{
    /**
     * @var RenaApp
     */
    protected $app;
    protected $db;
    protected $config;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $this->app->Db;
        $this->config = $this->app->baseConfig;
    }

    public function postApiFetch () {
      if(!isset($_GET['secret']) || $_GET['secret'] != $this->config->getConfig("fetcher", "secrets")) return;
      echo " init postApiFetch<br>";
      echo " + Step 1 : Update Character Affiliation<br>";
      $this->updateCharacterAffiliation();
      echo " + Step 2 : Convert new Notifications<br>";
      $this->convertNotifications();
    }

    function updateCharacterAffiliation () {
      // get characters with entry in db
      $dbchars = $this->db->query("SELECT easCharacters.characterID as oldID, ntCharacter.id as characterID, ntCharacter.name as characterName, ntCorporation.id as corporationID, ntCorporation.name as corporationName, ntAlliance.id as allianceID, ntAlliance.name as allianceName FROM easCharacters LEFT JOIN ntCharacter ON easCharacters.characterID = ntCharacter.id LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE ntCharacter.id IS NOT NULL");
      foreach ($dbchars as $dbchar) {
        $tmpchar = $this->app->CoreManager->getCharacter($dbchar['oldID']);
        $ch = $this->app->CoreManager->charChanged($tmpchar, $dbchar);
        if($ch || count($tmpchar->getGroups()) == 0) {
          $this->app->CoreManager->setBaseGroups($tmpchar);
        }
      }
      echo " + - ".count($dbchars)." old Characters updated<br>";
      // get characters without entry in db
      $charids = array();
      $specialchars = $this->db->query("SELECT easCharacters.characterID as characterID FROM easCharacters LEFT JOIN ntCharacter ON easCharacters.characterID = ntCharacter.id LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id WHERE ntCharacter.id IS NULL");
      foreach ($specialchars as $specialchar)
        array_push($charids, $specialchar['characterID']);

      // get affiliations from api
      $chunkedIdsFromAPI = array_chunk($charids, 100);
      $affs = array();
      for($i = 0; $i < count($chunkedIdsFromAPI); $i++) {
          $affs = array_merge($affs, $this->app->EVEEVECharacterAffiliation->getData($chunkedIdsFromAPI[$i])['result']['characters']);
      }

      $affsSorted = array();
      foreach($affs as $aff)
          $affsSorted[$aff['characterID']] = $aff;

      foreach ($affsSorted as $key => $affSorted) {
        $tmpchar = $this->app->CoreManager->getCharacter($key);
        $ch = $this->app->CoreManager->charChanged($tmpchar, $affSorted);
        if($ch || count($tmpchar->getGroups()) == 0) {
          $this->app->CoreManager->setBaseGroups($tmpchar);
        }
      }
      echo " + - ".count($affsSorted)." special Characters updated<br>";
    }

    function convertNotifications () {
      $notificationRows = $this->db->query(
        "SELECT DISTINCT ntNotification.notificationID as distID, ntNotification.*, ntNotificationRecipient.recipientID
        FROM ntNotification LEFT JOIN easNotifications ON ntNotification.notificationID = easNotifications.eveID, ntNotificationRecipient
        WHERE ntNotification.notificationID = ntNotificationRecipient.notificationID
        AND easNotifications.eveID IS NULL
        GROUP BY ntNotification.notificationID",
        array()
      );
      foreach ($notificationRows as $notificationRow) {
        $recipient = $this->app->CoreManager->getCharacter($notificationRow['recipientID']);
        $this->db->execute(
          "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
          array(
            ":eveID" => $notificationRow['notificationID'],
            ":state" => 0,
            ":typeID" => $notificationRow['typeID'],
            ":creatorID" => $notificationRow['senderID'],
            ":recipientID" => $recipient->getCorpId(),
            ":locationID" => 0,
            ":body" => $notificationRow['body'],
            ":created" => $notificationRow['sentDate'],
            ":requested" => $notificationRow['sentDate']
          )
        );
      }
      echo " + - ".count($notificationRows)." Notifications converted<br>";
    }

}
