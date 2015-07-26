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
        $notification['body']['againstName'] = isset($againstEntity) ? $againstEntity->getName() : "";
        $declareEntity = $app->CoreManager->getAlliance($notification['body']['declaredByID']);
        if(is_null($declareEntity))
            $declareEntity = $app->CoreManager->getCorporation($notification['body']['declaredByID']);
        $notification['body']['declaredByName'] = isset($declareEntity) ? $declareEntity->getName() : "";
        $notification['body']['costText'] = $notification['body']['cost'] != 0 ? GetByLabel('Notifications/bodyWar', $notification['body']) : '';
    }

    function FormatBillNotification (&$notification) {
        global $app, $billTypeMarketFine, $billTypeRentalBill, $billTypeBrokerBill, $billTypeWarBill, $billTypeAllianceMaintainanceBill, $billTypeSovereignityMarker;
        $billTypeID = $notification['body']['billTypeID'];
        if(!isset($notification['body']['currentDate']))
            $notification['body']['currentDate'] = $notification['created'];
        $creditorEntity = $app->CoreManager->getCorporation($notification['body']['creditorID']);
        if(!is_null($creditorEntity))
            $notification['body']['creditorsName'] = $creditorEntity->getName();
        else
            $notification['body']['creditorsName'] = $app->CoreManager->getLocation($notification['body']['creditorID'])->getName();
        $debtorEntity = $app->CoreManager->getCorporation($notification['body']['debtorID']);
        $notification['body']['debtorsName'] = $debtorEntity->getName();
        if($billTypeID == $billTypeMarketFine) {
            $messagePath = 'Notifications/bodyBillMarketFine';
        } else if($billTypeID == $billTypeRentalBill) {
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
        }
        $message = GetByLabel($messagePath, $notification['body']);
        $subject = GetByLabel('Notifications/subjBill', $notification['body']);
        return [$subject, $message];
    }