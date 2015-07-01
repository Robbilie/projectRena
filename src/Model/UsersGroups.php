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
        return $this->db->query("SELECT * FROM usersGroups u, groups g  WHERE u.groupID = g.groupID AND u.userID = :userID", array(":userID" => $userID));
    }

    /**
     * @param $userID
     * @param $groupID
     * @param $groupType
     *
     * @return bool|int|string
     */
    public function setGroup($userID, $groupID, $groupType)
    {
        return $this->db->execute("INSERT INTO usersGroups (userID, groupID, groupType) VALUES (:userID, :groupID, :groupType) ON DUPLICATE KEY UPDATE groupID = :groupID, groupType = :groupType", array(":userID" => $userID, ":groupID" => $groupID, ":groupType" => $groupType));
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
