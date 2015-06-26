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
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($fleets));
    }

    public function getFleet ($fleetID) {
      $fleet = array();
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($fleet));
    }

    public function confirmFleet ($hash) {
      $resp = array("state" => "error", "msg" => "");
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($resp));
    }

    public function createFleet () {
      $resp = array("state" => "error", "msg" => "");
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($resp));
    }

}
