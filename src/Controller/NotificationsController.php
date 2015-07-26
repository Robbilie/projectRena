<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class NotificationsController
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

    public function getTemplates () {
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(file_get_contents("./notificationtemplates.json"));
    }

    public function getUnreadCount () {
        $unreadcount = 0;
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $readcnt = $this->db->queryField(
                "SELECT count(id) as cnt
                FROM easNotifications LEFT JOIN easNotificationReaders ON easNotifications.id = easNotificationReaders.notificationID
                WHERE easNotificationReaders.readerID = :characterID", "cnt", array(":characterID" => $_SESSION['characterID']));
            $unreadcount = count($character->getNotifications()) - $readcnt;
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode(array("unread" => $unreadcount)));
    }

    public function getNotifications () {
        $notifications = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $notifications = $character->getCNotifications();
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($notifications));
    }

    public function getNotification ($notificationID) {
        $notification = null;
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $notification = $character->getCNotification($notificationID);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($notification));
    }

    public function getNotificationsByLocation ($locationID) {
        $notifications = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $id = $locationID;
            $notifications = $character->getCNotifications(function($i) use ($id) { return $i->getLocationId() == $id; });
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($notifications));
    }

    public function markAllAsRead () {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $charID = $char->getCharId();
            $notifications = $char->getNotifications();

            $vals = array_map(function ($a) use ($charID) { return is_null($a['readState']) ? '('.$a['id'].','.$charID.')' : null; } , $notifications);
            $imp = implode(",", $vals);
            $imp = str_replace(",,", ",", $imp);
            if(count($vals) > 0)
                $this->db->execute("INSERT INTO easNotificationReaders (notificationID, readerID) VALUES $imp");
            $resp['state'] = "success";
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    public function markAsRead ($notificationID) {
        $resp = array("state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $notification = $char->getCNotification($notificationID);
            if(!is_null($notification)) {
                if(!$notification->isRead())
                    $this->db->execute("INSERT INTO easNotificationReaders (notificationID, readerID) VALUES (:notificationID, :readerID)", array(":notificationID" => $notificationID, ":readerID" => $char->getCharId()));
                $resp['state'] = "success";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

}
