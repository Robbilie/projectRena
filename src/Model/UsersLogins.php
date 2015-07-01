<?php
namespace ProjectRena\Model;

use ProjectRena\RenaApp;

/**
 * Does stuff when the user logs in
 */
class UsersLogins
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
     * @param $ipAddress
     * @param null $ipHostname
     * @param null $ipCountry
     *
     * @return bool|int|string
     */
    public function updateIP($userID, $ipAddress, $ipHostname = null, $ipCountry = null)
    {
        return $this->db->execute("INSERT INTO usersLogins (userID, ipAddress, ipHostname, ipCountry) VALUES (:userID, :ipAddress, :ipHostname, :ipCountry) ON DUPLICATE KEY UPDATE userID = :userID, ipAddress = :ipAddress, ipHostname = :ipHostname, ipCountry = :ipCountry", array(":userID" => $userID, ":ipAddress" => $ipAddress, ":ipHostname" => $ipHostname, ":ipCountry" => $ipCountry));
    }
}
