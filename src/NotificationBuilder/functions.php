<?php

    namespace NotificationBuilder;
    
    function FormatAllWarDeclared (&$notification) {
        ParamAllWarNotificationWithCost($notification);
        $heading = GetByLabel('Notifications/subjWarDeclare', $notification['body']);
        if($notification['body']['hostileState']) {
            $message = GetByLabel('Notifications/bodyWarLegal', $notification['body']);
        } else {
            $message = GetByLabel('Notifications/bodyWarDelayed', $notification['body']);
        }
        return [$heading, $message];
    }

    function ParamAllWarNotificationWithCost (&$notification) {
        global $app;
        $againstEntity = $app->CoreManager->getAlliance($notification['body']['againstID']);
        if(is_null($againstEntity))
            $againstEntity = $app->CoreManager->getCorporation($notification['body']['againstID']);
        $notification['body']['againstName'] = isset($againstEntity) ? $againstEntity->getName() : "";
        $declareEntity = $app->CoreManager->getAlliance($notification['body']['declaredByID']);
        if(is_null($declareEntity))
            $declareEntity = $app->CoreManager->getCorporation($notification['body']['declaredByID']);
        $notification['body']['declaredByName'] = isset($declareEntity) ? $declareEntity->getName() : "";
        $notification['body']['costText'] = $notification['body']['cost'] != 0 ? GetByLabel('Notifications/bodyWar', $notification['body']) : '';
    }

    function FormatBillNotification (&$notification) {
        global $app, $billTypeMarketFine, $billTypeRentalBill, $billTypeBrokerBill, $billTypeWarBill, $billTypeAllianceMaintainanceBill, $billTypeSovereignityMarker, $billTypeInfrastructureHub;
        $notification['body']['notification_created'] = date('l, F j, Y', $notification['created']);
        $notification['body']['dueDate'] = date('l, F j, Y', (int)($notification['body']['dueDate'] / 10000000) - 11644473600);
        $notification['body']['currentDate'] = date('l, F j, Y', (int)($notification['body']['currentDate'] / 10000000) - 11644473600);
        $notification['body']['amount'] = number_format($notification['body']['amount'], 2, ",", ".")." ISK";

        $billTypeID = $notification['body']['billTypeID'];
        if(!isset($notification['body']['currentDate']))
            $notification['body']['currentDate'] = $notification['created'];
        $creditorEntity = $app->CoreManager->getCorporation($notification['body']['creditorID']);
        if(!is_null($creditorEntity))
            $notification['body']['creditorsName'] = $creditorEntity->getName();
        else
            $notification['body']['creditorsName'] = $app->CoreManager->getLocation($notification['body']['creditorID'], true)->getName();
        $debtorEntity = $app->CoreManager->getCorporation($notification['body']['debtorID']);
        $notification['body']['debtorsName'] = $debtorEntity->getName();
        if($billTypeID == $billTypeMarketFine) {
            $messagePath = 'Notifications/bodyBillMarketFine';
        } else if($billTypeID == $billTypeRentalBill) {
            $notification['body']['externalID'] = $app->CoreManager->getItemType($notification['body']['externalID'])->getName();
            $notification['body']['externalID2'] = $app->Db->queryField("SELECT stationName FROM ntOutpost WHERE stationID = :stationID", "stationName", array(":stationID" => $notification['body']['externalID2']));
            $messagePath = 'Notifications/bodyBillRental';
        } else if($billTypeID == $billTypeBrokerBill) {
            $messagePath = 'Notifications/bodyBillBroker';
        } else if($billTypeID == $billTypeWarBill) {
            $againstEntity = $app->CoreManager->getAlliance($notification['body']['externalID']);
            if(is_null($againstEntity))
                $againstEntity = $app->CoreManager->getCorporation($notification['body']['externalID']);
            $notification['body']['against'] = $againstEntity->getName();
            $messagePath = 'Notifications/bodyBillWar';
        } else if($billTypeID == $billTypeAllianceMaintainanceBill) {
            $notification['body']['allianceName'] = $app->CoreManager->getAlliance($notification['body']['externalID'])->getName();
            $messagePath = 'Notifications/bodyBillAllianceMaintenance';
        } else if($billTypeID == $billTypeSovereignityMarker) {
            $messagePath = 'Notifications/bodyBillSovereignty';
        } else if($billTypeID == $billTypeInfrastructureHub) {
            $notification['body']['externalID2'] = $app->CoreManager->getLocation($notification['body']['externalID2'], true)->getName();
            $messagePath = 'Notifications/bodyBillSovereignty';
        }
        $message = GetByLabel($messagePath, $notification['body']);
        $subject = GetByLabel('Notifications/subjBill', $notification['body']);
        return [$subject, !is_null($message) ? $message : $billTypeID];
    }

    function TowerFuelMsg (&$notification) {
        global $app;
        $pos = $app->CoreManager->getControltower($notification['body']['towerID']);
        $notification['body']['towerName'] = $pos->getName();
        $notification['body']['solarSystemName'] = $pos->getLocation()->getName();
        $notification['body']['leftHours'] = (int)(($notification['requested'] - time()) / 3600);
    }

    function WarSurrenderOffer (&$notification) {
        global $app;
        $notification['body']['iskOffered'] = $notification['body']['iskValue'];
        $owner1entity = $app->CoreManager->getAlliance($notification['body']['ownerID1']);
        if(is_null($owner1entity))
            $owner1entity = $app->CoreManager->getCorporation($notification['body']['ownerID1']);
        $notification['body']['owner1'] = $owner1entity->getName();
        
        $owner2entity = $app->CoreManager->getAlliance($notification['body']['ownerID2']);
        if(is_null($owner2entity))
            $owner2entity = $app->CoreManager->getCorporation($notification['body']['ownerID2']);
        $notification['body']['owner2'] = $owner2entity->getName();
    }

    function AcceptedSurrender (&$notification) {
        global $app;
        $notification['body']['iskOffer'] = $notification['body']['iskValue'];
        $entityEntity = $app->CoreManager->getAlliance($notification['body']['entityID']);
        if(is_null($entityEntity))
            $entityEntity = $app->CoreManager->getCorporation($notification['body']['entityID']);
        $notification['body']['entityName'] = $entityEntity->getName();

        $offeringEntity = $app->CoreManager->getAlliance($notification['body']['offeringID']);
        if(is_null($offeringEntity))
            $offeringEntity = $app->CoreManager->getCorporation($notification['body']['offeringID']);
        $notification['body']['offeringName'] = $offeringEntity->getName();

        $notification['body']['charName'] = $app->CoreManager->getCharacter($notification['body']['charID'])->getCharName();
    }

    function ParamAllWarNotification (&$notification) {
        global $app;

        $againstEntity = $app->CoreManager->getAlliance($notification['body']['againstID']);
        if(is_null($againstEntity))
            $againstEntity = $app->CoreManager->getCorporation($notification['body']['againstID']);
        $notification['body']['againstName'] = $againstEntity->getName();

        $declaredEntity = $app->CoreManager->getAlliance($notification['body']['declaredByID']);
        if(is_null($declaredEntity))
            $declaredEntity = $app->CoreManager->getCorporation($notification['body']['declaredByID']);
        $notification['body']['declaredByName'] = $declaredEntity->getName();
    }

    function ReactionProgressMsg (&$notification) {
        global $app;
        $pos = $app->CoreManager->getControltower($notification['body']['towerID']);
        $notification['body']['towerName'] = $pos->getName();
        $reaction = $app->CoreManager->getContainer($notification['body']['reactionID']);
        $notification['body']['reactionName'] = $reaction->getName();

        $notification['body']['progressMsg'] = $notification['body']["state"] == "running" ? (($notification['body']["value"] >= 0 ? "full" : "empty")." in ".((int)(($notification['requested'] - time()) / 3600))." hours") : "";
    }

    function ReactionInactiveMsg (&$notification) {
        global $app;
        $pos = $app->CoreManager->getControltower($notification['body']['towerID']);
        $notification['body']['towerName'] = $pos->getName();
        $reaction = $app->CoreManager->getContainer($notification['body']['reactionID']);
        $notification['body']['reactionName'] = $reaction->getName();
    }

    function ParamAllAnchoringNotification (&$notification) {
        global $app;
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
        $notification['body']['moonID'] = $app->CoreManager->getLocation($notification['body']['moonID'], true)->getName();
        $notification['body']['typeID'] = $app->CoreManager->getItemType($notification['body']['typeID'])->getName();

        $notification['body']['corpName'] = $app->CoreManager->getCorporation($notification['body']['corpID'])->getName();
        $notification['body']['otherTowersText'] = GetByLabel('Notifications/bodyPOSAnchoredNoTowers');

        if(!is_null($notification['body']['allianceID'])) {
            $notification['body']['allianceText'] = GetByLabel("Notifications/bodyPOSAnchoredAlliance", array("allianceName" => $app->CoreManager->getAlliance($notification['body']['allianceID'])->getName()));
        } else {
            $notification['body']['allianceText'] = "";
        }
        if(isset($notification['body']['corpsPresent']) && count($notification['body']['corpsPresent']) > 0) {
            $otherTowers = GetByLabel("Notifications/bodyPOSAnchoredOtherTowers");
            foreach ($notification['body']['corpsPresent'] as $corp) {
                if(count($corp['towers']) > 0) {
                    $allianceText = "";
                    if(!is_null($corp['allianceID'])) {
                        $allianceText = GetByLabel("Notifications/bodyPOSAnchoredOthersTowerAlliance", array("allianceName" => $app->CoreManager->getAlliance($corp['allianceID'])->getName()));
                    }
                    $otherTowers .= GetByLabel("Notifications/bodyPOSAnchoredTowersByCorp", array("towerCorp" => $app->CoreManager->getCorporation($corp['corpID'])->getName(), "allianceText" => $allianceText));
                    foreach ($corp['towers'] as $tower) {
                        $otherTowers .= GetByLabel("Notifications/bodyPOSAnchoredTower", array("moonID" => $app->CoreManager->getLocation($tower['moonID'], true)->getName(), "typeID" => $app->CoreManager->getItemType($tower['typeID'])->getName()));
                    }
                    $otherTowers .= '<br>';
                }
            }
            $notification['body']['otherTowersText'] = $otherTowers;
        }
    }

    function BillPaidCorpAllMsg (&$notification) {
        $notification['body']['notification_created'] = date('l, F j, Y', $notification['created']);
        $notification['body']['dueDate'] = date('l, F j, Y', (int)($notification['body']['dueDate'] / 10000000) - 11644473600);
        $notification['body']['amount'] = number_format($notification['body']['amount'], 2, ",", ".")." ISK";
    }

    function FormatTowerResourceAlertNotification (&$notification) {
        global $app;
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
        $notification['body']['moonID'] = $app->CoreManager->getLocation($notification['body']['moonID'], true)->getName();
        $notification['body']['typeID'] = $app->CoreManager->getItemType($notification['body']['typeID'])->getName();
        if(isset($notification['body']['corpID'])) {
            $notification['body']['corpName'] = $app->CoreManager->getCorporation($notification['body']['corpID'])->getName();
            $msg = GetByLabel('Notifications/bodyStarbaseLowResourcesCorp', array("corpName" => $notification['body']['corpName']));
            if(!is_null($notification['body']['allianceID'])) {
                $msg .= GetByLabel('Notifications/bodyStarbaseLowResourcesAlliance', array("allianceName" => $app->CoreManager->getAlliance($notification['body']['allianceID'])->getName()));
            }
            $notification['body']['corpAllianceText'] = $msg;
        } else {
            $notification['body']['corpAllianceText'] = "";
        }
        $message = GetByLabel('Notifications/bodyStarbaseLowResources', $notification['body']);
        foreach ($notification['body']['wants'] as &$want) {
            $want['typeID'] = $app->CoreManager->getItemType($want['typeID'])->getName();
            $message .= GetByLabel('Notifications/bodyStarbaseLowResourcesWants', $want);
        }
        return [GetByLabel('Notifications/subjStarbaseLowResources', $notification['body']), $message];
    }

    function _FormatSovCaptureNotification (&$notification) {
        global $app;
        if(!is_null($app->CoreManager->getItemType($notification['body']['structureTypeID'])))
            $notification['body']['structureTypeID'] = $app->CoreManager->getItemType($notification['body']['structureTypeID'])->getName();
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
    }

    function SovAllClaimAquiredMsg (&$notification) {
        global $app;
        $notification['body']['corporation'] = $app->CoreManager->getCorporation($notification['body']['corpID'])->getName();
        $notification['body']['alliance'] = $app->CoreManager->getAlliance($notification['body']['allianceID'])->getName();
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
    }

    function SovAllClaimLostMsg (&$notification) {
        global $app;
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
    }

    function ParamCloneActivation2Notification (&$notification) {
        global $app;
        $notification['body']['cloningServiceText'] = "";
        if($notification['body']['cloneStationID'] != $notification['body']['corpStationID'])
            $notification['body']['cloningServiceText'] = GetByLabel('Notifications/bodyCloneActivatedStationChange2', $notification['body']);
        $notification['body']['cloneStationID'] = $app->Db->queryField("SELECT stationName FROM ntOutpost WHERE stationID = :stationID", "stationName", array(":stationID" => $notification['body']['cloneStationID']));
        $notification['body']['corpStationID'] = $app->Db->queryField("SELECT stationName FROM ntOutpost WHERE stationID = :stationID", "stationName", array(":stationID" => $notification['body']['corpStationID']));
        $notification['body']['lastCloneText'] = "";
        if(!is_null($notification['body']['lastCloned'])) {
            $notification['body']['lastCloned'] = date('l, F j, Y', (int)($notification['body']['lastCloned'] / 10000000) - 11644473600);
            $notification['body']['lastCloneText'] = '<br><br>'.GetByLabel('Notifications/bodyCloneActivatedLastCloned2', $notification['body']);
        }
    }

    function ParamInsuranceFirstShipNotification (&$notification) {
        global $app, $deftypeHouseWarmingGift;
        $notification['body']['shipTypeID'] = $app->CoreManager->getItemType($notification['body']['shipTypeID'])->getName();
        if($notification['body']['isHouseWarmingGift'] == 0)
            $notification['body']['gift'] = $app->CoreManager->getItemType($deftypeHouseWarmingGift)->getName();
        else
            $notification['body']['gift'] = GetByLabel('Notifications/bodyNoobShipNoGift');
    }

    function BountyClaimMsg (&$notification) {
        global $app;
        $notification['body']['amount'] = number_format($notification['body']['amount'], 2, ",", ".")." ISK";
        $notification['body']['charID'] = $app->CoreManager->getCharacter($notification['body']['charID'])->getCharName();
    }

    function ParamFmtSovDamagedNotification (&$notification) {
        global $app;
        $notification['body']['shieldValue'] = (int)($notification['body']['shieldValue'] * 100);
        $notification['body']['armorValue'] = (int)($notification['body']['armorValue'] * 100);
        $notification['body']['hullValue'] = (int)($notification['body']['hullValue'] * 100);
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();

        if(!is_null($notification['body']['aggressorID']))
            $notification['body']['aggressor'] = $app->CoreManager->getCharacter($notification['body']['aggressorID'])->getCharName();
        else
            $notification['body']['aggressor'] = GetByLabel('UI/Common/Unknown');

        if(!is_null($notification['body']['aggressorCorpID']))
            $notification['body']['aggressorCorp'] = $app->CoreManager->getCorporation($notification['body']['aggressorCorpID'])->getName();
        else
            $notification['body']['aggressorCorp'] = GetByLabel('UI/Common/Unknown');

        if(!is_null($notification['body']['aggressorAllianceID']))
            $notification['body']['aggressorAlliance'] = $app->CoreManager->getAlliance($notification['body']['aggressorAllianceID'])->getName();
        else
            $notification['body']['aggressorAlliance'] = GetByLabel('UI/Common/Unknown');
    }

    function ParamStationAggression2Notification (&$notification) {
        global $app;
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
        $notification['body']['stationID'] = $app->Db->queryField("SELECT stationName FROM ntOutpost WHERE stationID = :stationID", "stationName", array(":stationID" => $notification['body']['stationID']));
        
        $notification['body']['shieldDamage'] = (int)($notification['body']['shieldValue'] * 100);
        $notification['body']['armorDamage'] = (int)($notification['body']['armorValue'] * 100);
        $notification['body']['hullDamage'] = (int)($notification['body']['hullValue'] * 100);
        $notification['body']['aggressor'] = GetByLabel('UI/Common/Unknown');
        $notification['body']['aggressorCorp'] = GetByLabel('UI/Common/Unknown');
        $notification['body']['aggressorAlliance'] = GetByLabel('UI/Common/Unknown');

        if(!is_null($notification['body']['aggressorID']))
            $notification['body']['aggressor'] = $app->CoreManager->getCharacter($notification['body']['aggressorID'])->getCharName();

        if(!is_null($notification['body']['aggressorCorpID']))
            $notification['body']['aggressorCorp'] = $app->CoreManager->getCorporation($notification['body']['aggressorCorpID'])->getName();

        if(!is_null($notification['body']['aggressorAllianceID']))
            $notification['body']['aggressorAlliance'] = $app->CoreManager->getAlliance($notification['body']['aggressorAllianceID'])->getName();
        else
            $notification['body']['aggressorAlliance'] = GetByLabel('UI/Common/CorporationNotInAlliance');
    }

    function FormatTowerAlertNotification (&$notification) {
        global $app;
        $notification['body']['solarSystemID'] = $app->CoreManager->getLocation($notification['body']['solarSystemID'], true)->getName();
        $notification['body']['typeID'] = $app->CoreManager->getItemType($notification['body']['typeID'])->getName();

        if(!is_null($notification['body']['moonID']))
            $notification['body']['moonName'] = $app->CoreManager->getLocation($notification['body']['moonID'], true)->getName();
        else
            $notification['body']['moonName'] = GetByLabel('Notifications/UnknownSystem');

        $message = GetByLabel('Notifications/bodyStarbaseDamageLocation', $notification['body']);

        if(!is_null($notification['body']['shieldValue']) && !is_null($notification['body']['armorValue']) && !is_null($notification['body']['hullValue'])) {
            $notification['body']['shieldDamage'] = (int)($notification['body']['shieldValue'] * 100);
            $notification['body']['armorDamage'] = (int)($notification['body']['armorValue'] * 100);
            $notification['body']['hullDamage'] = (int)($notification['body']['hullValue'] * 100);
            $message .= GetByLabel('Notifications/bodyStarbaseDamageValues', $notification['body']);
        } else {
            $message .= GetByLabel('Notifications/bodyStarbaseDamageMissing');
        }

        if(!is_null($notification['body']['aggressorID'])) {
            $char = $app->CoreManager->getCharacter($notification['body']['aggressorID']);
            if(!is_null($char)) {
                $notification['body']['aggressorID'] = $char->getCharName();
                $notification['body']['agressorCorp'] = $app->CoreManager->getCorporation($notification['body']['aggressorCorpID'])->getName();
                $notification['body']['agressorAlliance'] = '-';
                if(!is_null($notification['body']['aggressorAllianceID']))
                    $notification['body']['agressorAlliance'] = $app->CoreManager->getAlliance($notification['body']['aggressorAllianceID'])->getName();
                $message .= GetByLabel('Notifications/bodyStarbaseDamageAttacker', $notification['body']);
            } else {
                $message .= GetByLabel('Notifications/bodyStarbaseDamageAgressorNotCharacter', array("aggressorName" => $app->CoreManager->getCorporation($notification['body']['aggressorID'])->getName()));
            }
        }
        return [GetByLabel('Notifications/subjStarbaseDamage', $notification['body']), $message];
    }

    function BountyYourBountyClaimed (&$notification) {
        global $app;
        $notification['body']['victim'] = $app->CoreManager->getCharacter($notification['body']['victimID'])->getCharName();
        $notification['body']['bountyPaid'] = number_format($notification['body']['bounty'], 2, ",", ".")." ISK";
    }

    function ContactAdd (&$notification) {
        global $app;
        $notification['body']['messageText'] = $notification['body']['message'] ? $notification['body']['message'] : '';
        $notification['body']['notification_senderID'] = $app->CoreManager->getCharacter($notification['creatorID'])->getCharName();
    }

    function CorpAppNewMsg (&$notification) {
        global $app;
        $notification['body']['charID'] = $app->CoreManager->getCharacter($notification['body']['charID'])->getCharName();
    }

    function CorpNewCEOMsg (&$notification) {
        global $app;
        $notification['body']['oldCeoID'] = $app->CoreManager->getCharacter($notification['body']['oldCeoID'])->getCharName();
        $notification['body']['newCeoID'] = $app->CoreManager->getCharacter($notification['body']['newCeoID'])->getCharName();
        $notification['body']['charID'] = $app->CoreManager->getCharacter($notification['body']['charID'])->getCharName();
        $notification['body']['corporationName'] = $app->CoreManager->getCorporation($notification['body']['corpID'])->getName();
    }

    function ParamFmtInsurancePayout (&$notification) {
        global $app;
        if($notification['body']['payout'])
            $notification['body']['defaultPayoutText'] = GetByLabel('Notifications/bodyInsurancePayoutDefault');
        $notification['body']['amount'] = number_format($notification['body']['amount'], 2, ",", ".")." ISK";
    }