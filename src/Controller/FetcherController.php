<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;
use ProjectRena\Model\Core\CoreReactionModule;

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
        echo " + Step 3 : Generate POS Notifications<br>";
        $this->generatePOSNotfications();
    }

    function updateCharacterAffiliation () {
        // get characters with entry in db
        $dbchars = $this->db->query(
            "SELECT
                easCharacters.characterID as oldID,
                ntCharacter.id as characterID,
                ntCharacter.name as characterName,
                ntCorporation.id as corporationID,
                ntCorporation.name as corporationName,
                ntAlliance.id as allianceID,
                ntAlliance.name as allianceName
            FROM easCharacters
            LEFT JOIN ntCharacter ON easCharacters.characterID = ntCharacter.id
            LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id
            LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id
            WHERE ntCharacter.id IS NOT NULL"
        );
        foreach ($dbchars as $dbchar) {
            $tmpchar = $this->app->CoreManager->getCharacter($dbchar['oldID']);
            $ch = $this->app->CoreManager->charChanged($tmpchar, $dbchar);
            if($ch || count($tmpchar->getGroups()) == 0) {
                $tmpchar->setBaseGroups();
            }
        }
        echo " + - ".count($dbchars)." old Characters updated<br>";
        // get characters without entry in db
        $charids = array();
        $specialchars = $this->db->query(
            "SELECT easCharacters.characterID as characterID
            FROM easCharacters
            LEFT JOIN ntCharacter ON easCharacters.characterID = ntCharacter.id
            LEFT JOIN ntCorporation ON ntCharacter.corporation = ntCorporation.id
            LEFT JOIN ntAlliance ON ntCorporation.alliance = ntAlliance.id
            WHERE ntCharacter.id IS NULL"
        );
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
                $tmpchar->setBaseGroups();
            }
        }
        echo " + - ".count($affsSorted)." special Characters updated<br>";
    }

    function convertNotifications () {
        $corpNotificationTypes = [5, 7, 8, 10, 13, 16, 22, 27, 41, 43, 45, 75, 76, 80, 86, 87, 88, 95, 103, 123, 147, 1337001, 1337002, 1337003];
        $charNotifcationTypes = [14, 34, 35, 89, 111, 138, 140];
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
            if(in_array($notificationRow['typeID'], $corpNotificationTypes) || in_array($notificationRow['typeID'], $charNotifcationTypes))
                $this->db->execute(
                    "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                    array(
                        ":eveID"        => $notificationRow['notificationID'],
                        ":state"        => 0,
                        ":typeID"       => $notificationRow['typeID'],
                        ":creatorID"    => $notificationRow['senderID'],
                        ":recipientID"  => in_array($notificationRow['typeID'], $corpNotificationTypes) ? $recipient->getCorpId() : $recipient->getCharId(),
                        ":locationID"   => 0,
                        ":body"         => json_encode(yaml_parse($notificationRow['body'])),
                        ":created"      => $notificationRow['sentDate'],
                        ":requested"    => $notificationRow['sentDate']
                    )
                );
        }
        echo " + - ".count($notificationRows)." Notifications converted<br>";
    }

    function generatePOSNotfications () {
        $posRows = $this->db->query("SELECT ntItem.itemID FROM ntItem, ntItemStarbase WHERE ntItem.itemID = ntItemStarbase.itemID");
        foreach ($posRows as $posRow) {
            $pos = $this->app->CoreManager->getControltower($posRow['itemID']);
            $lastNotif = $this->app->CoreManager->getNotificationByLocation($pos->getId());

            $h = 0;
            $hDif = 0;
            $fuelTS = 0;

            $fuelResourceCnt = 0;
            $posResources = $pos->getResources();
            foreach ($posResources as $posResource)
                if($posResource['purpose'] == 1)
                    $fuelResourceCnt = (int)$posResource['quantity'];

            $content = $pos->getContent();
            foreach ($content as $item) {
    			if($item->getType()->getGroupId() == 1136) {
                    $hasSov = $pos->getLocation()->getOwner() && $pos->getLocation()->getOwner()->getId() == $pos->getOwner()->getCAlliance()->getId();
                    $fuelConsume = ceil(($hasSov ? .75 : 1) * $fuelResourceCnt);
                    $hours = (int)($item->getQuantity() / $fuelConsume);
                    $fuelTS = $item->getTimestamp();
    			}
    		}

            $isOutdated = false;

            if(!is_null($lastNotif)) {
                $hDif = $lastNotif->getRequested() - $fuelTS;
                $hDif /= 3600;
                $hDif = floor($hDif);
                if($hours != floor(($lastNotif->getRequested() - $lastNotif->getCreated()) / 3600)) {
                    $isOutdated = true;
                    if($lastNotif->getState() != 2)
                        $this->app->CoreManager->deleteNotification($lastNotif->getId());
                }
            }

            $createTask = (is_null($lastNotif) || $isOutdated) ? true : false;

            echo "<div> - ".$createTask." | ".$pos->getId()." | ".floor((time() + ($hours * 3600)) / 3600)." | ".(!is_null($lastNotif) ? floor($lastNotif->getRequested() / 3600) : "new")." - </div>";

            if($createTask) {
                $this->db->execute(
                    "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                    array(
                        ":eveID"        => 0,
                        ":state"        => 0,
                        ":typeID"       => 1337001,
                        ":creatorID"    => 0,
                        ":recipientID"  => $pos->getOwnerId(),
                        ":locationID"   => $pos->getId(),
                        ":body"         => json_encode(array("towerID" => $pos->getId())),
                        ":created"      => $fuelTS,
                        ":requested"    => $fuelTS + ($hours * 3600)
                    )
                );
            }

            // reactions

            $reactionRows = $this->db->query("SELECT * FROM easControltowerReactions WHERE towerID = :towerID", array(":towerID" => $pos->getId()));

            $reactions = array();

            foreach ($reactionRows as $reactionRow) {
                $isOutput = count($this->db->queryRow("SELECT * FROM easControltowerReactions WHERE source = :source AND towerID = :towerID", array(":source" => $reactionRow['destination'], ":towerID" => $pos->getId()))) == 0;
                if($isOutput)
                    array_push($reactions, new CoreReactionModule($this->app, array("container" => $this->app->CoreManager->getContainer((int)$reactionRow['destination']), "tower" => $pos)));
            }

            foreach ($reactions as $reaction) {
                $reaction->populateInputs();
            }

            foreach ($reactions as $reaction) {
                $reaction->processReaction();
            }

            foreach ($reactions as $reaction) {
                $exportedArr = array();
                $reaction->exportReaction($exportedArr);

                foreach ($exportedArr as $silo) {

                    echo $pos->getName().",".$pos->getMoon()->getName()." -> ".$this->app->CoreManager->getContainer($silo['id'])->getName()." is ".($silo['state'] == "running" ? ("running ".($silo["value"] >= 0 ? "full" : "empty")." in ".$silo["left"]." hours") : $silo['state'])."<br>";

                    $lastSiloNotif = $this->app->CoreManager->getNotificationByLocation($silo['id']);

                    if(!is_null($lastSiloNotif) && $silo['ts'] > $lastSiloNotif->getCreated()) {

                        echo " - recalculate<br>";

                        $tsOffset = floor(($lastSiloNotif->getRequested() - ($silo['ts'] + ($silo['left'] * 3600))) / 3600);

                        if($silo['state'] == "running") {

                            echo " - - running<br>";

                            if($tsOffset == 0 || $tsOffset == -1) {
		                         echo " - - - correct<br>";
    						} else {

    							echo " - - - incorrect {$tsOffset}<br>";

                                $this->db->execute("UPDATE easNotifications SET state = 2 WHERE locationID = :locationID AND state = 0", array(":locationID" => $silo['id']));

                                if(($lastSiloNotif->getRequested() - $lastSiloNotif->getCreated()) / 3600 == $silo['left']) {

                                    $this->db->execute(
                                        "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                                        array(
                                            ":eveID"        => 0,
                                            ":state"        => 0,
                                            ":typeID"       => 1337003,
                                            ":creatorID"    => 0,
                                            ":recipientID"  => $pos->getOwnerId(),
                                            ":locationID"   => $silo['id'],
                                            ":body"         => json_encode(array("reactionID" => $silo['id'], "towerID" => $pos->getId())),
                                            ":created"      => $silo['ts'],
                                            ":requested"    => $silo['ts']
                                        )
                                    );

                                } else {

                                    $this->db->execute(
                                        "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                                        array(
                                            ":eveID"        => 0,
                                            ":state"        => 0,
                                            ":typeID"       => 1337002,
                                            ":creatorID"    => 0,
                                            ":recipientID"  => $pos->getOwnerId(),
                                            ":locationID"   => $silo['id'],
                                            ":body"         => json_encode(array("reactionID" => $silo['id'], "towerID" => $pos->getId(), "state" => $silo['state'], "value" => $silo['value'])),
                                            ":created"      => $silo['ts'],
                                            ":requested"    => $silo['ts'] + ($silo['left'] * 3600)
                                        )
                                    );

                                }

                            }
                        } else {

						    echo " - - inactive<br>";

                            if($lastSiloNotif->getTypeId() == 1337003) {
                                echo " - - - correct<br>";
                            } else {
                                echo " - - - incorrect<br>";
                                $this->db->execute("UPDATE easNotifications SET state = 2 WHERE locationID = :locationID AND state = 0", array(":locationID" => $silo['id']));

                                $this->db->execute(
                                    "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                                    array(
                                        ":eveID"        => 0,
                                        ":state"        => 0,
                                        ":typeID"       => 1337003,
                                        ":creatorID"    => 0,
                                        ":recipientID"  => $pos->getOwnerId(),
                                        ":locationID"   => $silo['id'],
                                        ":body"         => json_encode(array("reactionID" => $silo['id'], "towerID" => $pos->getId())),
                                        ":created"      => $silo['ts'],
                                        ":requested"    => $silo['ts']
                                    )
                                );
                            }

                        }

                    } else if (is_null($lastSiloNotif)) {
                        echo " - new<br>";

                        if($silo['state'] == "running") {
                            echo " - - running<br>";

                            $this->db->execute(
                                "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                                array(
                                    ":eveID"        => 0,
                                    ":state"        => 0,
                                    ":typeID"       => 1337002,
                                    ":creatorID"    => 0,
                                    ":recipientID"  => $pos->getOwnerId(),
                                    ":locationID"   => $silo['id'],
                                    ":body"         => json_encode(array("reactionID" => $silo['id'], "towerID" => $pos->getId(), "state" => $silo['state'], "value" => $silo['value'])),
                                    ":created"      => $silo['ts'],
                                    ":requested"    => $silo['ts'] + ($silo['left'] * 3600)
                                )
                            );

                        } else {
                            echo " - - inactive : ".$silo['state']."<br>";

                            $this->db->execute(
                                "INSERT INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
                                array(
                                    ":eveID"        => 0,
                                    ":state"        => 0,
                                    ":typeID"       => 1337003,
                                    ":creatorID"    => 0,
                                    ":recipientID"  => $pos->getOwnerId(),
                                    ":locationID"   => $silo['id'],
                                    ":body"         => json_encode(array("reactionID" => $silo['id'], "towerID" => $pos->getId())),
                                    ":created"      => $silo['ts'],
                                    ":requested"    => $silo['ts']
                                )
                            );

                        }

                    } else if(!is_null($lastSiloNotif) && $silo['ts'] == $lastSiloNotif->getCreated()) {
                        echo " - old<br>";
    					if($silo['state'] == "running") {
    						echo " - - running<br>";
    					} else {
    						echo " - - inactive<br>";
    					}
                    }

                }
            }


        }
    }

}
