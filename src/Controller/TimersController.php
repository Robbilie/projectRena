<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class TimersController
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

    public function getTimers () {
      $timers = array("timers" => array(), "cancreate" => array());
      if(isset($_SESSION["loggedIn"])) {
        $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
        if($char->hasPermission("readTimers", "corporation")) {
          $corpTimers = $this->app->CoreManager->getTimers($char->getCorpId());
          $timers = array_merge($timers['timers'], $corpTimers);
          array_push($timers['cancreate'], "corporation");
        }
        if($char->hasPermission("readTimers", "alliance")) {
          array_push($timers['cancreate'], "alliance");
          array_push($timers['cancreate'], "blue");
        }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($timers));
    }

    public function createTimer () {
      $resp = array("state" => "error", "msg" => "");
      if(isset($_SESSION["loggedIn"])) {
        if(isset($_POST['scope']) && $_POST['scope'] != "" && isset($_POST['typeID']) && $_POST['typeID'] != "" && isset($_POST['locationID']) && $_POST['locationID'] != "" && isset($_POST['rf']) && $_POST['rf'] != "" && isset($_POST['comment']) && isset($_POST['timestamp']) && $_POST['timestamp'] != "") {
          $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
          if($char->hasPermission("writeTimers", $_POST['scope'] == "blue" ? "alliance" : $_POST['scope'])) {
            $timer = $this->app->CoreManager->createTimer($_POST['scope'], $char->getCharId(), $_POST['scope'] == "corporation" ? $char->getCorpId() : $char->getAlliId(), (int)$_POST['typeID'], (int)$_POST['locationID'], (int)$_POST['rf'], $_POST['comment'], (int)$_POST['timestamp']);
            if (!is_null($timer)) {
              $resp['state'] = "success";
            } else {
              $resp['msg'] = "Something went wrong.";
            }
          } else {
  				  $resp['msg'] = "You are not permitted to do this.";
  			  }
  		  } else {
  			  $resp['msg'] = "Missing Parameters.";
  		  }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($resp));
    }

}
