<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Group CRUD
 */
class Groups
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
     * @param $groupID
     * @param $groupName
     * @param int $ownerID
     *
     * @return bool|int|string
     */
    public function updateGroup($groupID, $groupName, $ownerID = 0)
    {
        return $this->db->execute("INSERT INTO groups (groupID, groupName, ownerID) VALUES (:groupID, :groupName, :ownerID) ON DUPLICATE KEY UPDATE groupID = :groupID, groupName = :groupName, ownerID = :ownerID", array(":groupID" => $groupID, ":groupName" => $groupName, ":ownerID" => $ownerID));
    }

    /**
     * @param $groupID
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getGroupByID($groupID)
    {
        return $this->db->query("SELECT * FROM groups WHERE groupID = :groupID", array(":groupID" => $groupID));
    }

    /**
     * @param $groupName
     *
     * @return array|bool
     * @throws \Exception
     */
    public function getGroupByName($groupName)
    {
        return $this->db->query("SELECT * FROM groups WHERE groupName = :groupName", array(":groupName" => $groupName));
    }

    /**
     * @param $groupID
     *
     * @return bool|int|string
     */
    public function deleteGroupByID($groupID)
    {
        return $this->db->execute("DELETE FROM groups WHERE groupID = :groupID", array(":groupID" => $groupID));
    }

    /**
     * @param $groupName
     *
     * @return bool|int|string
     */
    public function deleteGroupByName($groupName)
    {
        return $this->db->execute("DELETE FROM groups WHERE groupName = :groupName", array(":groupName" => $groupName));
    }

    /**
     * @param $groupID
     * @param array $admins
     *
     * @return bool|int|string
     */
    public function setAdmins($groupID, $admins = array())
    {
        return $this->db->execute("INSERT INTO groups (groupID, admins) VALUES (:groupID, :admins) ON DUPLICATE KEY UPDATE groupID = :groupID, admins = :admins", array(":groupID" => $groupID, ":admins" => json_encode($admins)));
    }

    /**
     * @param $groupID
     * @param array $moderators
     *
     * @return bool|int|string
     */
    public function setModerators($groupID, $moderators = array())
    {
        return $this->db->execute("INSERT INTO groups (groupID, moderators) VALUES (:groupID, :moderators) ON DUPLICATE KEY UPDATE groupID = :groupID, moderators = :moderators", array(":groupID" => $groupID, ":moderators" => json_encode($moderators)));
    }

    /**
     * @param $groupID
     *
     * @return mixed
     */
    public function getAdmins($groupID)
    {
        return json_decode($this->db->queryField("SELECT admins FROM groups WHERE groupID = :groupID", array(":groupID" => $groupID)), true);
    }

    /**
     * @param $groupID
     *
     * @return mixed
     */
    public function getModerators($groupID)
    {
        return json_decode($this->db->queryField("SELECT moderators FROM groups WHERE groupID = :groupID", array(":groupID" => $groupID)), true);
    }
}
