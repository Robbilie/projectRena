<?php

    namespace NotificationBuilder;
    
    function FormatAllWarDeclared (&$notification) {
    //ParamAllWarNotificationWithCost($notification);
        $heading = GetByLabel('Notifications/subjWarDeclare', $notification);
        if($notification['body']['hostileState']) {
            $message = GetByLabel('Notifications/bodyWarLegal', $notification);
        } else {
            $message = GetByLabel('Notifications/bodyWarDelayed', $notification);
        }
        return [$heading, $message];
    }
