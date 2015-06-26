<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class IntelController
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

    public function getFleets () {
      $fleets = array();
      if(isset($_SESSION['loggedin'])) {
        $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
        $fleets = $char->getCFleets();
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($fleets));
    }

    public function getFleet ($fleetID) {
      $rfleet = array();
      if(isset($_SESSION['loggedin'])) {
        $fleet = $this->app->CoreManager->getFleet($fleetID);
        if($fleet->hasParticipant($_SESSION['characterID'])) {
          $rfleet = $fleet;
        }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($rfleet));
    }

    public function confirmFleet ($hash) {
      $resp = array("state" => "error", "msg" => "");
      if(isset($_SESSION['loggedin'])) {
        $fleet = $this->app->CoreManager->getFleetByHash($hash);
        if($fleet->isExpired()) {
          $resp['msg'] = "Participation Link has expired.";
        } else {
          if($fleet->hasParticipant($_SESSION['characterID'])) {
            $fleet->confirmParticipant($_SESSION['characterID']);
            $resp['state'] = "success";
          } else {
            $resp['msg'] = "You did not participate in this fleet.";
          }
        }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($resp));
    }

    public function createFleet () {
      $resp = array("state" => "error", "msg" => "");
      if(isset($_SESSION['loggedin'])) {
        $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
        if($char->hasPermission("createFleet")) {
          $fleet = $this->app->CoreManager->createFleet($_POST['name'], $_POST['comment'], $_SESSION['characterID'], $_POST['expiresin']);
          if(!is_null($fleet)) {
            $resp['state'] = "success";
          } else {
            $resp['msg'] = "Something went wrong.";
          }
        } else {
          $resp['msg'] = "You are not permitted to do this.";
        }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($resp));
    }

}
