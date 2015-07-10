<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class SearchController
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

    // search for a system named like
    public function findSystemNames ($name) {
        $systemRows = $this->db->query("SELECT solarSystemName as name, solarSystemID as data FROM mapSolarSystems WHERE solarSystemName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($systemRows));
    }

    // search for a character named like
    public function findCharacterNames ($name) {
        $characterRows = $this->db->query("SELECT characterName as name, characterID as data FROM easCharacters WHERE characterName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($characterRows));
    }

    // search for an invname like
    public function findInvNames ($name) {
        $invRows = $this->db->query("SELECT itemName as name, itemID as data FROM invNames WHERE itemName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($invRows));
    }

    // search for an invtypename like
    public function findInvTypeNames ($name) {
        $invRows = $this->db->query("SELECT typeName as name, typeID as data FROM invTypes WHERE typeName LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($invRows));
    }

    // search for corp/alli names like
    public function findCorpAlliNames ($name) {
        $corpAlliRows = $this->db->query("SELECT name, id as data FROM ntCorporation WHERE name LIKE :name UNION SELECT name, id as data FROM ntAlliance WHERE name LIKE :name", array(":name" => $name."%"));
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($corpAlliRows));
    }

}
