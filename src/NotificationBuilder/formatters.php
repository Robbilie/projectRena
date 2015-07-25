<?php

    $formatters = [
        $notificationTypeOldLscMessages =>
            ['Notifications/subjLegacy', 'Notifications/bodyLegacy'],

        $notificationTypeCharTerminationMsg =>
            ['Notifications/subjCharacterTermination', 'Notifications/bodyCharacterTermination',
                function (&$data) { return ParamCharacterTerminationNotification($data); }],

         $notificationTypeCharMedalMsg =>
             ['Notifications/subjCharacterMedal', 'Notifications/bodyCharacterMedal',
                 function (&$data) { return ParamCharacterMedalNotification($data); }],

         $notificationTypeAllMaintenanceBillMsg =>
             ['Notifications/subjMaintenanceBill', 'Notifications/bodyMaintenanceBill',
                 function (&$data) { return array('allianceName' => CreateItemInfoLink($data['allianceID'])); }],

         $notificationTypeAllWarDeclaredMsg => function (&$data) { return FormatAllWarDeclared($data); },

         $notificationTypeAllWarCorpJoinedAllianceMsg =>
             ['Notifications/subjWarCorpJoinAlliance', 'Notifications/bodyWarCorpJoinAlliance',
                 function (&$data) { return  array('alliance' => CreateItemInfoLink($data['allianceID']),
                                                'corporation' => CreateItemInfoLink($data['corpID'])); }],

         $notificationTypeAllyJoinedWarDefenderMsg =>
             ['Notifications/subjWarAllyJoinedWar', 'Notifications/bodyWarAllyJoinedWarDefender',
                 function (&$data) { return array('ally' => CreateItemInfoLink($data['allyID']),
                                             'aggressor' => CreateItemInfoLink($data['aggressorID']),
                                             'time' => $data['startTime']); }],

         $notificationTypeAllyJoinedWarAggressorMsg =>
             ['Notifications/subjWarAllyJoinedWar', 'Notifications/bodyWarAllyJoinedWarAggressor',
                 function (&$data) { return array('ally' => CreateItemInfoLink($data['allyID']),
                                              'defender' => CreateItemInfoLink($data['defenderID']),
                                              'time' => $data['startTime']); }],

         $notificationTypeAllyJoinedWarAllyMsg =>
             ['Notifications/subjWarAllyJoinedWar', 'Notifications/bodyWarAllyJoinedWarAlly',
                 function (&$data) { return array('ally' => CreateItemInfoLink($data['allyID']),
                                         'defender' => CreateItemInfoLink($data['defenderID']),
                                         'aggressor' => CreateItemInfoLink($data['aggressorID']),
                                         'time' => $data['startTime']); }],

         $notificationTypeMercOfferedNegotiationMsg =>
             ['Notifications/subjMercOfferedContract', 'Notifications/bodyMercOfferedContract',
                 function (&$data) { return array('merc' => CreateItemInfoLink($data['mercID']),
                                              'defender' => CreateItemInfoLink($data['defenderID']),
                                              'aggressor' => CreateItemInfoLink($data['aggressorID']),
                                              'iskOffered' => evefmt.FmtISK($data['iskValue'])); }],

        $notificationTypeWarSurrenderOfferMsg =>
            ['Notifications/subjWarSurrenderOffer', 'Notifications/bodyWarSurrenderOffer',
                function (&$data) { return array('owner1' => CreateItemInfoLink($data['ownerID1']),
                                         'owner2' => CreateItemInfoLink($data['ownerID1']),
                                         'iskOffered' => evefmt.FmtISK($data['iskValue'])); }],

         $notificationTypeWarSurrenderDeclinedMsg =>
             ['Notifications/subjWarSurrenderDeclined', 'Notifications/bodyWarSurrenderDeclined',
                 function (&$data) { return array('owner' => CreateItemInfoLink($data['ownerID']),
                                            'iskOffered' => evefmt.FmtISK($data['iskValue'])); }],

         $notificationTypeAllyContractCancelled =>
             ['Notifications/subjWarAllyContractCancelled', 'Notifications/bodyWarAllyContractCancelled',
                 function (&$data) { return array('defender' => CreateItemInfoLink($data['defenderID']),
                                          'aggressor' => CreateItemInfoLink($data['aggressorID']),
                                          'time' => fmtutil.FmtDate($data['timeFinished'])); }],

         $notificationTypeWarAllyOfferDeclinedMsg =>
             ['Notifications/subjWarAllyOfferDeclined', 'Notifications/bodyWarAllyOfferDeclined',
                 function (&$data) { return array('defender' => CreateItemInfoLink($data['defenderID']),
                                            'aggressor' => CreateItemInfoLink($data['aggressorID']),
                                            'ally' => CreateItemInfoLink($data['allyID']),
                                            'char' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeAllWarSurrenderMsg =>
            ['Notifications/subjWarSurender', 'Notifications/bodyWarSunrender',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeAllWarRetractedMsg =>
            ['Notifications/subjWarRetracts', 'Notifications/bodyWarRetract',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeAllWarInvalidatedMsg =>
            ['Notifications/subjWarConcordInvalidates', 'Notifications/bodyWarConcordInvalidates',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeCharBillMsg => function (&$data) { return FormatBillNotification($data); },

        $notificationTypeCorpAllBillMsg => function (&$data) { return FormatBillNotification($data); },

        $notificationTypeBillOutOfMoneyMsg =>
            ['Notifications/subjBillOutOfMoney', 'Notifications/bodyBillOutOfMoney',
                function (&$data) { return array('billType' => cfg.billtypes.Get($data['billTypeID']).billTypeName); }],

        $notificationTypeBillPaidCharMsg =>
            ['Notifications/subjBillPaid', 'Notifications/bodyBillPaid'],

        $notificationTypeBillPaidCorpAllMsg =>
            ['Notifications/subjBillPaid', 'Notifications/bodyBillPaid'],

        $notificationTypeBountyClaimMsg =>
            ['Notifications/subjBountyPayment', 'Notifications/bodyBountyPayment'],

        $notificationTypeCloneActivationMsg =>
            ['Notifications/subjCloneActivated', 'Notifications/bodyCloneActivated',
                function (&$data) { return PramCloneActivationNotification($data); }],

        $notificationTypeCloneActivationMsg2 =>
            ['Notifications/subjCloneActivated2', 'Notifications/bodyCloneActivated2',
                function (&$data) { return ParamCloneActivation2Notification($data); }],

        $notificationTypeCorpAppNewMsg =>
            ['Notifications/subjCorpApplicationNew', 'Notifications/bodyApplicationNew'],

        $notificationTypeCorpAppRejectMsg =>
            ['Notifications/subjCorpAppRejected', 'Notifications/bodyCorpAppRejected',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],


        $notificationTypeCorpAppRejectCustomMsg =>
            ['Notifications/subjCorpAppRejected', 'Notifications/bodyCorpAppCustomRejected',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID']),
                                               'customMessage' => $data['customMessage']); }],

        $notificationTypeCorpAppAcceptMsg =>
            ['Notifications/subjCorpAppAccepted', 'Notifications/bodyCorpAppAccepted',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCharAppAcceptMsg =>
            ['Notifications/subjCharAppAccepted', 'Notifications/bodyCharAppAccepted',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCharAppRejectMsg =>
            ['Notifications/subjCharAppRejected', 'Notifications/bodyCharAppRejected',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCharAppWithdrawMsg =>
            ['Notifications/subjCharAppWithdrawn', 'Notifications/bodyCharAppWithdrawn',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpAppInvitedMsg =>
            ['Notifications/subjCorpAppInvited', 'Notifications/bodyCorpAppInvited',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeDustAppAcceptedMsg =>
            ['Notifications/subjDustAppAccepted', 'Notifications/bodyDustAppAccepted',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpKicked =>
            ['Notifications/Corporations/KickedTitle', 'Notifications/Corporations/KickedBody',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpTaxChangeMsg =>
            ['Notifications/subjCorpTaxRateChange', 'Notifications/bodyCorpTaxRateChange',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpNewsMsg => function (&$data) { return FormatCorpNewsNotification($data); },

        $notificationTypeCharLeftCorpMsg =>
            ['Notifications/subjCharLeftCorp', 'Notifications/bodyCharLeftCorp',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpNewCEOMsg =>
            ['Notifications/subjCEOQuit', 'Notifications/bodyCEOQuit',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpLiquidationMsg =>
            ['Notifications/subjCorpLiquidation', 'Notifications/bodyCorpLiquidation',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpDividendMsg =>
            ['Notifications/subjCorpPayoutDividends', 'Notifications/bodyLegacy',
                function (&$data) { return ParamFmtCorpDividendNotification($data); }],

        $notificationTypeCorpVoteMsg =>
            ['Notifications/subjCorpVote', 'Notifications/bodyLegacy'],

        $notificationTypeCorpVoteCEORevokedMsg =>
            ['Notifications/subjCEORollRevoked', 'Notifications/bodyCEORollRevoked',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCorpWarDeclaredMsg =>
            ['Notifications/subjWarDeclare', 'Notifications/bodyWarDeclare',
                function (&$data) { return ParamAllWarNotificationWithCost($data); }],

        $notificationTypeCorpWarFightingLegalMsg =>
            ['Notifications/subjWarDeclare', 'Notifications/bodyWarLegal',
                function (&$data) { return ParamAllWarNotificationWithCost($data); }],

        $notificationTypeCorpWarSurrenderMsg =>
            ['Notifications/subjWarSurender', 'Notifications/bodyWarSunrender',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeCorpWarRetractedMsg =>
            ['Notifications/subjWarRetracts', 'Notifications/bodyWarRetract',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeCorpWarInvalidatedMsg =>
            ['Notifications/subjWarConcordInvalidates', 'Notifications/bodyWarConcordInvalidates',
                function (&$data) { return ParamAllWarNotification($data); }],

        $notificationTypeContainerPasswordMsg =>
            ['Notifications/subjContainerPasswordChanged', 'Notifications/bodyContainerPasswordChanged',
                function (&$data) { return ParamContainerPasswordNotification($data); }],

        $notificationTypeCustomsMsg =>
            ['Notifications/subjContrabandConfiscation', 'Notifications/bodyContrabandConfiscation',
                function (&$data) { return ParamCustomsNotification($data); }],

        $notificationTypeInsuranceFirstShipMsg =>
            ['Notifications/subjNoobShip', 'Notifications/bodyNoobShip',
                function (&$data) { return ParamInsuranceFirstShipNotification($data); }],

        $notificationTypeInsurancePayoutMsg =>
            ['Notifications/subjInsurancePayout', 'Notifications/bodyInsurancePayout',
                function (&$data) { return ParamFmtInsurancePayout($data); }],

        $notificationTypeInsuranceInvalidatedMsg =>
            ['Notifications/subjInsuranceInvalid', 'Notifications/bodyInsuranceInvalid',
                function (&$data) { return ParamInsuranceInvalidatedNotification($data); }],

        $notificationTypeSovAllClaimFailMsg =>
            ['Notifications/subjSovClaimFailed', 'Notifications/bodyLegacy',
                function (&$data) { return ParamSovAllClaimFailNotification($data); }],

        $notificationTypeSovCorpClaimFailMsg =>
            ['Notifications/subjSovClaimFailed', 'Notifications/bodyLegacy',
                function (&$data) { return ParamSovAllClaimFailNotification($data); }],

        $notificationTypeSovAllBillLateMsg =>
            ['Notifications/subjSovBillLate', 'Notifications/bodySovBillLate',
                function (&$data) { return array('corporation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeSovCorpBillLateMsg =>
            ['Notifications/subjSovBillLate', 'Notifications/bodySovBillLate',
                function (&$data) { return array('corporation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeSovAllClaimLostMsg =>
            ['Notifications/subjSovAllianceClaimLost', 'Notifications/bodySovAllianceClaimLost'],

        $notificationTypeSovCorpClaimLostMsg =>
            ['Notifications/subjSovCorporationClaimLost', 'Notifications/bodySovCorporationClaimLost'],

        $notificationTypeSovAllClaimAquiredMsg =>
            ['Notifications/subjSovClaimAquiredAlliance', 'Notifications/bodySovClaimAquiredAlliance',
                function (&$data) { return array('corporation' => CreateItemInfoLink($data['corpID']),
                                              'alliance' => CreateItemInfoLink($data['allianceID'])); }],

        $notificationTypeSovCorpClaimAquiredMsg =>
            ['Notifications/subjSovClaimAquiredCorporation', 'Notifications/bodySovClaimAquiredCorporation',
                function (&$data) { return array('corporation' => CreateItemInfoLink($data['corpID']),
                                               'alliance' => CreateItemInfoLink($data['allianceID'])); }],

        $notificationTypeAllAnchoringMsg =>
            ['Notifications/subjPOSAnchored', 'Notifications/bodyPOSAnchored',
                function (&$data) { return ParamAllAnchoringNotification($data); }],

        $notificationTypeAllStructVulnerableMsg =>
            ['Notifications/subjSovVulnerable', 'Notifications/bodySovVulnerable'],

        $notificationTypeAllStrucInvulnerableMsg =>
            ['Notifications/subjSovNotVulnerable', 'Notifications/bodySovNotVulnerable'],

        $notificationTypeSovDisruptorMsg =>
            ['Notifications/subjSovDisruptionDetected', 'Notifications/bodySovDisruptionDetected'],

        $notificationTypeCorpStructLostMsg =>
            ['Notifications/subjInfraStructureLost', 'Notifications/bodyInfraStructureLost'],

        $notificationTypeCorpOfficeExpirationMsg =>
            ['Notifications/SubjCorpOfficeExpires', 'Notifications/bodyCorpOfficeExpires',
                function (&$data) { return ParamCorpOfficeExpiration($data); }],

        $notificationTypeCloneRevokedMsg1 =>
            ['Notifications/subjClone', 'Notifications/bodyCloneRevoked1',
                function (&$data) { return array('managerStation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCloneMovedMsg =>
            ['Notifications/subjClone', 'Notifications/bodyCloneMoved',
                function (&$data) { return array('corporation' => CreateItemInfoLink($data['charsInCorpID']),
                                      'managerStation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeCloneRevokedMsg2 =>
            ['Notifications/subjClone', 'Notifications/bodyCloneRevoke2',
                function (&$data) { return array('managerStation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeInsuranceExpirationMsg =>
            ['Notifications/subjInsuranceExpired', 'Notifications/bodyInsuranceExpired'],

        $notificationTypeInsuranceIssuedMsg =>
            ['Notifications/subjInsuranceIssued', 'Notifications/bodyInsuranceIssued',
                function (&$data) { return array('typeID2' => $data['typeID']); }],

        $notificationTypeJumpCloneDeletedMsg1 =>
            ['Notifications/subjCloneJumpImplantDestruction', 'Notifications/bodyCloneJumpImplantDestruction',
                function (&$data) { return ParamJumpCloneDeleted1Notification($data); }],

        $notificationTypeJumpCloneDeletedMsg2 =>
            ['Notifications/subjCloneJumpImplantDestruction', 'Notifications/bodyCloneJumpImplantDestruction',
                function (&$data) { return ParamJumpCloneDeleted2Notification($data); }],

        $notificationTypeFWCorpJoinMsg =>
            ['Notifications/subjFacWarCorpJoin', 'Notifications/bodyFacWarCorpJoin',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWCorpLeaveMsg =>
            ['Notifications/subjFacWarCorpLeave', 'Notifications/bodyFacWarCorpLeave',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWCorpKickMsg =>
            ['Notifications/subjFacWarCorpKicked', 'Notifications/bodyFacWarCorpKicked',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWCharKickMsg =>
            ['Notifications/subjFacWarCharKicked', 'Notifications/bodyFacWarCharKicked',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWAllianceKickMsg =>
            ['Notifications/subjFacWarAllianceKicked', 'Notifications/bodyFacWarAllianceKicked',
                function (&$data) { return ParamFmtFactionWarfareAlliances($data); }],

        $notificationTypeFWCorpWarningMsg =>
            ['Notifications/subjFacWarCorpWarrning', 'Notifications/bodyFacWarCorpWarrning',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWCharWarningMsg =>
            ['Notifications/subjFacWarCharWarrning', 'Notifications/bodyFacWarCharWarrning',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFWAllianceWarningMsg =>
            ['Notifications/subjFacWarCorpWarrning', 'Notifications/bodyFacWarAllianceWarrning',
                function (&$data) { return ParamFmtFactionWarfareAlliances($data); }],

        $notificationTypeFWCharRankLossMsg => function (&$data) { return FormatFWCharRankLossNotification($data); },

        $notificationTypeFWCharRankGainMsg => function (&$data) { return FormatFWCharRankGainNotification($data); },

        $notificationTypeAgentMoveMsg =>
            ['Notifications/subjLegacy', 'Notifications/bodyLegacy'],

        $notificationTypeTransactionReversalMsg =>
            ['Notifications/subjMassReversal', 'Notifications/bodyLegacy'],

        $notificationTypeReimbursementMsg => function (&$data) { return FormatShipReimbursementMessage($data); },

        $notificationTypeLocateCharMsg => function (&$data) { return FormatLocateCharNotification($data); },

        $notificationTypeResearchMissionAvailableMsg =>
            ['Notifications/subjResearchMissionAvailable', 'Notifications/bodyResearchMissionAvailable'],

        $notificationTypeMissionOfferExpirationMsg => function (&$data) { return FormatMissionOfferExpiredNotification($data); },

        $notificationTypeMissionTimeoutMsg =>
            ['Notifications/subjMissionTimeout', 'Notifications/bodyMissionTimeout'],

        $notificationTypeStoryLineMissionAvailableMsg =>
            ['Notifications/subjStoryLineMissionAvilable', 'Notifications/bodyLegacy',
                function (&$data) { return ParamStoryLineMissionAvailableNotification($data); }],

        $notificationTypeTowerAlertMsg => function (&$data) { return FormatTowerAlertNotification($data); },

        $notificationTypeTowerResourceAlertMsg => function (&$data) { return FormatTowerResourceAlertNotification($data); },

        $notificationTypeStationAggressionMsg1 =>
            ['Notifications/subjOutpostAgression', 'Notifications/bodyOutpostAgression',
                function (&$data) { return ParamStationAggression1Notification($data); }],

        $notificationTypeStationStateChangeMsg =>
            ['Notifications/subjLegacy', 'Notifications/bodyOutpostService',
                function (&$data) { return ParamStationStateChangeNotification($data); }],

        $notificationTypeStationConquerMsg =>
            ['Notifications/subjOutpostConquered', 'Notifications/bodyOutpostConquered',
                function (&$data) { return ParamStationConquerNotification($data); }],

        $notificationTypeStationAggressionMsg2 =>
            ['Notifications/subjOutpostAgressed', 'Notifications/bodyOutpostAgressed',
                function (&$data) { return ParamStationAggression2Notification($data); }],

        $notificationTypeFacWarCorpJoinRequestMsg =>
            ['Notifications/subjFacWarCorpJoinRequest', 'Notifications/bodyFacWarCorpJoinRequest',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFacWarCorpLeaveRequestMsg =>
            ['Notifications/subjFacWarCorpLeaveRequest', 'Notifications/bodyFacWarCorpLeaveRequest',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFacWarCorpJoinWithdrawMsg =>
            ['Notifications/subjFacWarCorpJoinWithdraw', 'Notifications/bodyFacWarCorpJoinWithdraw',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeFacWarCorpLeaveWithdrawMsg =>
            ['Notifications/subjFacWarCorpLeaveWithdraw', 'Notifications/bodyjFacWarCorpLeaveWithdraw',
                function (&$data) { return ParamFmtFactionWarfareCorps($data); }],

        $notificationTypeSovereigntyTCUDamageMsg =>
            ['Notifications/subjSovTCUDamaged', 'Notifications/bodySovTCUDamaged',
                function (&$data) { return ParamFmtSovDamagedNotification($data); }],

        $notificationTypeSovereigntySBUDamageMsg =>
            ['Notifications/subjSovSBUDamaged', 'Notifications/bodySovSBUDamaged',
                function (&$data) { return ParamFmtSovDamagedNotification($data); }],

        $notificationTypeSovereigntyIHDamageMsg =>
            ['Notifications/subjSovIHDamaged', 'Notifications/bodySovIHDamaged',
                function (&$data) { return ParamFmtSovDamagedNotification($data); }],

        $notificationTypeContactAdd =>
            ['Notifications/subjContactAdd', 'Notifications/bodyContactAdd',
                function (&$data) { return array('messageText' => $data.get('message', ''),
                                   'level' => GetRelationshipName($data['level'])); }],

        $notificationTypeContactEdit =>
            ['Notifications/subjContactEdit', 'Notifications/bodyContactEdit',
                function (&$data) { return array('messageText' => $data.get('message', ''),
                                    'level' => GetRelationshipName($data['level'])); }],

        $notificationTypeIncursionCompletedMsg =>
            ['Notifications/subjIncursionComplete', 'Notifications/bodyIncursionComplete',
                function (&$data) { return ParamIncursionCompletedNotification($data); }],

        $notificationTypeTutorialMsg => function (&$data) { return FormatTutorialNotification($data); },

        $notificationTypeOrbitalAttacked =>
            ['Notifications/subjOrbitalAttacked', 'Notifications/bodyOrbitalAttacked',
                function (&$data) { return FormatOrbitalAttackedNotification($data); }],

        $notificationTypeOrbitalReinforced =>
            ['Notifications/subjOrbitalReinforced', 'Notifications/bodyOrbitalReinforced',
                function (&$data) { return FormatOrbitalReinforcedNotification($data); }],

        $notificationTypeOwnershipTransferred =>
            ['Notifications/subjOwnershipTransferred', 'Notifications/bodyOwnershipTransferred'],

        $notificationTypeFacWarLPPayoutKill =>
            ['Notifications/FacWar/subjLPPayout', 'Notifications/FacWar/bodyLPPayoutKill',
                function (&$data) { return array('location' => CreateLocationInfoLink($data['locationID'], typeSolarSystem),
                                           'victim' => CreateItemInfoLink($data['charRefID']),
                                           'corporation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeFacWarLPDisqualifiedKill =>
            ['Notifications/FacWar/subjLPDisqualified', 'Notifications/FacWar/bodyLPDisqualifiedKill',
                function (&$data) { return array('location' => CreateLocationInfoLink($data['locationID'], typeSolarSystem),
                                                 'victim' => CreateItemInfoLink($data['charRefID']),
                                                 'corporation' => CreateItemInfoLink($data['corpID'])); }],

        $notificationTypeFacWarLPPayoutEvent => function (&$data) { return FormatFacWarLPPayout($data); },

        $notificationTypeFacWarLPDisqualifiedEvent => function (&$data) { return FormatFacWarLPDisqualified($data); },

        $notificationTypeBountyYourBountyClaimed =>
            ['Notifications/subjBountyYourBountyClaimed', 'Notifications/bodyBountyYourBountyClaimed',
                function (&$data) { return array('victim' => CreateItemInfoLink($data['victimID']),
                                                'bountyPaid' => evefmt.FmtISK($data['bounty'], 0)); }],

        $notificationTypeBountyPlacedChar =>
            ['Notifications/subjBountyPlacedChar', 'Notifications/bodyBountyPlacedChar',
                function (&$data) { return array('bountyPlacer' => CreateItemInfoLink($data['bountyPlacerID']),
                                         'amount' => evefmt.FmtISK($data['bounty'], 0)); }],

        $notificationTypeBountyPlacedCorp =>
            ['Notifications/subjBountyPlacedCorp', 'Notifications/bodyBountyPlacedCorp',
                function (&$data) { return array('bountyPlacer' => CreateItemInfoLink($data['bountyPlacerID']),
                                         'amount' => evefmt.FmtISK($data['bounty'], 0)); }],

        $notificationTypeBountyPlacedAlliance =>
            ['Notifications/subjBountyPlacedAlliance', 'Notifications/bodyBountyPlacedAlliance',
                function (&$data) { return array('bountyPlacer' => CreateItemInfoLink($data['bountyPlacerID']),
                                             'amount' => evefmt.FmtISK($data['bounty'], 0)); }],

        $notificationTypeKillRightAvailable =>
            ['Notifications/subjKillRightSale', 'Notifications/bodyKillRightSale',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID']),
                                           'amount' => evefmt.FmtISK($data['price'], 0),
                                           'availableToName' => CreateItemInfoLink($data['toEntityID'])); }],

        $notificationTypeKillRightAvailableOpen =>
            ['Notifications/subjKillRightSaleOpen', 'Notifications/bodyKillRightSaleOpen',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID']),
                                               'amount' => evefmt.FmtISK($data['price'], 0)); }],

        $notificationTypeKillRightEarned =>
            ['Notifications/subjKillRightEarned', 'Notifications/bodyKillRightEarned',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeKillRightUsed =>
            ['Notifications/subjKillRightUsed', 'Notifications/bodyKillRightUsed',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeKillRightUnavailable =>
            ['Notifications/subjKillRightUnavailable', 'Notifications/bodyKillRightUnavailable',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID']),
                                             'availableToName' => CreateItemInfoLink($data['toEntityID'])); }],

        $notificationTypeKillRightUnavailableOpen =>
            ['Notifications/subjKillRightUnavailableAll', 'Notifications/bodyKillRightUnavailableAll',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeDeclareWar =>
            ['Notifications/subjDeclareWar', 'Notifications/bodyDeclareWar',
                function (&$data) { return array('defenderName' => CreateItemInfoLink($data['defenderID']),
                                   'entityName' => CreateItemInfoLink($data['entityID']),
                                   'charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeOfferedSurrender =>
            ['Notifications/subjOfferedSurrender', 'Notifications/bodyOfferedSurrender',
                function (&$data) { return array('entityName' => CreateItemInfoLink($data['entityID']),
                                         'offeredName' => CreateItemInfoLink($data['offeredID']),
                                         'charName' => CreateItemInfoLink($data['charID']),
                                         'iskOffer' => evefmt.FmtISK($data['iskValue'], 0)); }],

        $notificationTypeAcceptedSurrender =>
            ['Notifications/subjAcceptedSurrender', 'Notifications/bodyAcceptedSurrender',
                function (&$data) { return array('entityName' => CreateItemInfoLink($data['entityID']),
                                          'offeringName' => CreateItemInfoLink($data['offeringID']),
                                          'charName' => CreateItemInfoLink($data['charID']),
                                          'iskOffer' => evefmt.FmtISK($data['iskValue'], 0)); }],

        $notificationTypeMadeWarMutual =>
            ['Notifications/subjMadeWarMutual', 'Notifications/bodyMadeWarMutual',
                function (&$data) { return array('enemyName' => CreateItemInfoLink($data['enemyID']),
                                      'charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeRetractsWar =>
            ['Notifications/subjRetractsWar', 'Notifications/bodyRetractsWar',
                function (&$data) { return array('enemyName' => CreateItemInfoLink($data['enemyID']),
                                    'charName' => CreateItemInfoLink($data['charID'])); }],

        $notificationTypeOfferedToAlly =>
            ['Notifications/subjOfferedToAlly', 'Notifications/bodyOfferedToAlly',
                function (&$data) { return array('defenderName' => CreateItemInfoLink($data['defenderID']),
                                      'enemyName' => CreateItemInfoLink($data['enemyID']),
                                      'charName' => CreateItemInfoLink($data['charID']),
                                      'iskOffer' => evefmt.FmtISK($data['iskValue'], 0)); }],

        $notificationTypeAcceptedAlly =>
            ['Notifications/subjAcceptedAlly', 'Notifications/bodyAcceptedAlly',
                function (&$data) { return array('allyName' => CreateItemInfoLink($data['allyID']),
                                     'enemyName' => CreateItemInfoLink($data['enemyID']),
                                     'charName' => CreateItemInfoLink($data['charID']),
                                     'iskOffer' => evefmt.FmtISK($data['iskValue'], 0),
                                     'joinTime' => fmtutil.FmtDate($data['time'])); }],

        /*
        $notificationTypeDistrictAttacked =>
            ['Notifications/subjDistrictAttacked', 'Notifications/bodyDistrictAttacked',
                function (&$data) { return array('DistrictName' => localization.GetImportantByLabel('UI/Locations/LocationDistrictFormatter', solarSystemID=$data['solarSystemID'], romanCelestialIndex=fmtutil.IntToRoman($data['celestialIndex']), districtIndex=$data['districtIndex']),
                                         'BattleTime' => fmtutil.FmtDate($data['startDate']),
                                         'AttackingCorporation' => CreateItemInfoLink($data['attackerID'])); }],
        */

        $notificationTypeBattlePunishFriendlyFire =>
            ['Notifications/subjBattlePunishFriendlyFire', 'Notifications/bodyBattlePunishFriendlyFire',
                function (&$data) { return array('corporationName' => CreateItemInfoLink($data['corporationID']),
                                                 'standingsChange' => $data['standingsChange'],
                                                 'hours' => $data['hours']); }],

        $notificationTypeBountyESSTaken =>
            ['Notifications/subjBountyESSTaken', 'Notifications/bodyBountyESSTaken',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID']),
                                       'totalAmount' => evefmt.FmtISK($data['totalIsk'], 0),
                                       'iskAmount' => evefmt.FmtISK($data['myIsk'], 0)); }],

        $notificationTypeBountyESSShared =>
            ['Notifications/subjBountyESSShared', 'Notifications/bodyBountyESSShared',
                function (&$data) { return array('charName' => CreateItemInfoLink($data['charID']),
                                        'totalAmount' => evefmt.FmtISK($data['totalIsk'], 0),
                                        'iskAmount' => evefmt.FmtISK($data['myIsk'], 0)); }],

        $notificationTypeIndustryTeamAuctionWon =>
            ['Notifications/subjIndustryTeamAuctionWon', 'Notifications/bodyIndustryTeamAuctionWon',
                function (&$data) { return _GetIndustryTeamDict(d); }],

        $notificationTypeIndustryTeamAuctionLost =>
            ['Notifications/subjIndustryTeamAuctionLost', 'Notifications/bodyIndustryTeamAuctionLost',
                function (&$data) { return _GetIndustryTeamDict(d); }],

        $notificationTypeCorpFriendlyFireEnableTimerStarted =>
            ['Notifications/subjCorpFriendlyFireEnableTimerStarted', 'Notifications/bodyCorpFriendlyFireEnableTimerStarted',
                function (&$data) { return _FormatFriendlyFireChangeStarted($data); }],

        $notificationTypeCorpFriendlyFireDisableTimerStarted =>
            ['Notifications/subjCorpFriendlyFireDisableTimerStarted', 'Notifications/bodyCorpFriendlyFireDisableTimerStarted',
                function (&$data) { return _FormatFriendlyFireChangeStarted($data); }],

        $notificationTypeCorpFriendlyFireEnableTimerCompleted =>
            ['Notifications/subjCorpFriendlyFireEnableTimerCompleted', 'Notifications/bodyCorpFriendlyFireEnableTimerCompleted',
                function (&$data) { return _FormatFriendlyFireCompleted($data); }],

        $notificationTypeCorpFriendlyFireDisableTimerCompleted =>
            ['Notifications/subjCorpFriendlyFireDisableTimerCompleted', 'Notifications/bodyCorpFriendlyFireDisableTimerCompleted',
                function (&$data) { return _FormatFriendlyFireCompleted($data); }],

        $notificationTypeEntosisCaptureStarted =>
            ['Notifications/subjSovereigntyCaptureStarted', 'Notifications/bodySovereigntyCaptureStarted',
                function (&$data) { return _FormatSovCaptureNotification($data); }],

        $notificationTypeStationServiceEnabled =>
            ['Notifications/subjSovereigntyServiceEnabled', 'Notifications/bodySovereigntyServiceEnabled',
                function (&$data) { return _FormatSovCaptureNotification($data); }],

        $notificationTypeStationServiceDisabled =>
            ['Notifications/subjSovereigntyServiceDisabled', 'Notifications/bodySovereigntyServiceDisabled',
                function (&$data) { return _FormatSovCaptureNotification($data); }],

        $notificationTypeStationServiceHalfCaptured =>
            ['Notifications/subjSovereigntyServiceHalfCapture', 'Notifications/bodySovereigntyServiceHalfCapture',
                function (&$data) { return _FormatSovCaptureNotification($data); }]
    ];
