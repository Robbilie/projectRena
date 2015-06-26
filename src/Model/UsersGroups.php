<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * User group management CRUD
 */
class UsersGroups
{

    /**
     * The Slim Application
     */
    private $app;

    /**
     * The Cache
     */
    private $cache;

    /**
     * The baseConfig (config/config.php)
     */
    private $config;

    /**
     * cURL interface (getData / setData)
     */
    private $curl;

    /**
     * The Database
     */
    private $db;

    /**
     * The logger, outputs to logs/app.log
     */
    private $log;

    /**
     * StatsD for tracking stats
     */
    private $statsd;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $app->Db;
        $this->config = $app->baseConfig;
        $this->cache = $app->Cache;
        $this->curl = $app->cURL;
        $this->statsd = $app->StatsD;
        $this->log = $app->Logging;
    }

    /**
     * @param $userID
     *
     * @return array
     * @throws \Exception
     */
    public function getGroup($userID)
    {
        $groupIDs = array();
        $data = $this->db->query("SELECT groupID FROM usersGroups WHERE userID = :userID", array(":userID" => $userID));
        foreach($data as $grpID)
            $groupIDs[] = $grpID["groupID"];

        return $groupIDs;
    }

    /**
     * @param $userID
     * @param $groupID
     *
     * @return bool|int|string
     */
    public function setGroup($userID, $groupID)
    {
        return $this->db->execute("INSERT INTO usersGroups (userID, groupID) VALUES (:userID, :groupID) ON DUPLICATE KEY UPDATE groupID = :groupID", array(":userID" => $userID, ":groupID" => $groupID));
    }

    /**
     * @param $userID
     * @param $groupID
     *
     * @return bool|int|string
     */
    public function deleteGroup($userID, $groupID)
    {
        return $this->db->execute("DELETE FROM usersGroups WHERE userID = :userID AND groupID = :groupID", array(":userID" => $userID, ":groupID" => $groupID));
    }
}
