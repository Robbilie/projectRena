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

    public function getNotifications () {
      $notifications = array();
      if(isset($_SESSION['loggedin'])) {
        $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
        $notifications = $character->getCNotifications();
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($notifications));
    }

    public function getNotification ($notificationID) {
      $notification = null;
      if(isset($_SESSION['loggedin'])) {
        $notification = $this->app->CoreManager->getNotification($notificationID);
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($notification));
    }

}
