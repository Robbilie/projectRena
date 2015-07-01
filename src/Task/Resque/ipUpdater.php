<?php
namespace ProjectRena\Task\Resque;

/**
 * Updates the IP hostname and location in the usersLogin table, this way ipinfo.io can go down without the user will experience login lag
 */
class ipUpdater
{
    /**
     * The Slim Application
     */
    private $app;

    /**
     * Performs the task, can access all $this->crap setup in setUp)
     */
    public function perform()
    {
        $userID = $this->args["userID"];
        $ipAddress = $this->args["ip"];

        $data = json_decode($this->app->cURL->getData("http://ipinfo.io/{$ipAddress}/json"), true);
        $ipHostname = isset($data["hostname"]) ? $data["hostname"] : "";
        $ipCountry = isset($data["country"]) ? $data["country"] : "";
        $this->app->Db->execute("INSERT INTO usersLogins (userID, ipAddress, ipHostname, ipCountry) VALUES (:userID, :ipAddress, :ipHostname, :ipCountry) ON DUPLICATE KEY UPDATE userID = :userID, ipAddress = :ipAddress, ipHostname = :ipHostname, ipCountry = :ipCountry", array(":userID" => $userID, ":ipAddress" => $ipAddress, ":ipHostname" => $ipHostname, ":ipCountry" => $ipCountry));
    }

    /**
     * Sets up the task (Setup $this->crap and such here)
     */
    public function setUp()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();
    }

    /**
     * Tears the task down, unset $this->crap and such
     */
    public function tearDown()
    {
        $this->app = null;
    }
}
