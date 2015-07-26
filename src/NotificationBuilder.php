<?php

    namespace NotificationBuilder;

    require_once __DIR__.'/NotificationBuilder/const.php';
    require_once __DIR__.'/NotificationBuilder/functions.php';
    require_once __DIR__.'/NotificationBuilder/formatters.php';


    $notificationData = json_decode(file_get_contents(__DIR__."/NotificationBuilder/notifications.json"), true);

    function GetByLabel ($label, $params = array()) {
        global $notificationData;
        $tempNotification;
        foreach ($notificationData as $notification) {
            if($notification['label'] == explode("/", $label)[1]) {
                $tempNotification = $notification;
                break;
            }
        }
        $message = $tempNotification['message'][0];
        if(!is_null($tempNotification['message'][2]))
            foreach ($tempNotification['message'][2] as $varName => $varValue)
                if(isset($params[$varValue['variableName']]))
                    $message = str_replace($varName, $params[$varValue['variableName']], $message);
        return $message;
    }

    function format (&$notification) {
        global $formatters;
        if(isset($formatters[$notification['typeID']]) && in_array($notification['typeID'], [5, 10])) {
            // strange if

            $subject;
            $body;

            if(is_array($formatters[$notification['typeID']])) { // is a normal array
                if(count($formatters[$notification['typeID']]) > 2) { // has a lambda
                    $formatters[$notification['typeID']][2]($notification);
                }
                $subject = GetByLabel($formatters[$notification['typeID']][0], $notification['body']);
                $body = GetByLabel($formatters[$notification['typeID']][1], $notification['body']);
            } else { // is a function
                $retArr = $formatters[$notification['typeID']]($notification);
                $subject = $retArr[0];
                $body = $retArr[1];
            }

            return [$subject, $body];
        } else {
            $subject = GetByLabel("Notifications/subjBadNotificationMessage");
            $body = GetByLabel('Notifications/bodyBadNotificationMessage', array("id" => $notification['id']));
            return [$subject, $body];
        }
    }
