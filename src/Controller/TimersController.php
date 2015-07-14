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
                array_push($timers['cancreate'], "corporation");
                $corpTimers = $this->app->CoreManager->getTimers($char->getCorpId());
                $timers['timers'] = array_merge($timers['timers'], $corpTimers);
            }
            if($char->hasPermission("readTimers", "alliance")) {
                array_push($timers['cancreate'], "alliance");
                array_push($timers['cancreate'], "blue");
                $alliTimers = $this->app->CoreManager->getTimers($char->getAlliId());
                $timers['timers'] = array_merge($timers['timers'], $alliTimers);
                $r = $this->db->query(
                    "SELECT ownerID FROM ntContactList WHERE
                    (
                        contactID = :characterID OR
                        contactID = :corporationID OR
                        contactID = :allianceID
                    ) AND
                    standing > 0",
                    array(
                        ":characterID" => $char->getCharId(),
                        ":corporationID" => $char->getCorpId(),
                        ":allianceID" => $char->getAlliId()
                    )
                );
                foreach ($r as $row) {
                    $blueTimers = $this->app->CoreManager->getTimers($row['ownerID']);
                    $timers['timers'] = array_merge($timers['timers'], $blueTimers);
                }
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($timers));
    }

    public function createTimer () {
        $resp = array("state" => "error", "msg" => "");
        if(isset($_SESSION["loggedIn"])) {
            if(isset($_POST['scope']) && $_POST['scope'] != "" && isset($_POST['ownerID']) && $_POST['ownerID'] != "" && isset($_POST['typeID']) && $_POST['typeID'] != "" && isset($_POST['locationID']) && $_POST['locationID'] != "" && isset($_POST['rf']) && $_POST['rf'] != "" && isset($_POST['comment']) && isset($_POST['timestamp']) && $_POST['timestamp'] != "") {
                $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
                if($char->hasPermission("writeTimers", $_POST['scope'] == "blue" ? "alliance" : $_POST['scope'])) {
                    $timer = $this->app->CoreManager->createTimer($_POST['scope'], (int)($_POST['scope'] == "corporation" ? $char->getCorpId() : $char->getAlliId()), (int)$_POST['ownerID'], (int)$_POST['typeID'], (int)$_POST['locationID'], (int)$_POST['rf'], $_POST['comment'], (int)strtotime($_POST['timestamp']));
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
