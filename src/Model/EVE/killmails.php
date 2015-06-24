<?php
namespace ProjectRena\Model\EVE;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class killmails
{
    /**
     * @var RenaApp
     */
    private $app;
    /**
     * @var \ProjectRena\Lib\Db
     */
    private $db;

    /**
     * @param RenaApp $app
     */
    function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $this->app->Db;
    }

    /**
     * @param $killID
     *
     * @return array
     */
    public function getAllByID($killID)
    {
        return $this->db->queryRow("SELECT * FROM killmails WHERE killID = :killID", array(":killID" => $killID), 60);
    }

    /**
     * @param $killID
     *
     * @return null
     */
    public function getProcessedByID($killID)
    {
        return $this->db->queryField("SELECT processed FROM killmails WHERE killID = :killID", "processed", array(":killID" => $killID));
    }

    /**
     * @param $killID
     *
     * @return null
     */
    public function getHashByID($killID)
    {
        return $this->db->queryField("SELECT hash FROM killmails WHERE killID = :killID", "hash", array(":killID" => $killID));
    }

    /**
     * @param $killID
     *
     * @return null
     */
    public function getSourceByID($killID)
    {
        return $this->db->queryField("SELECT source FROM killmails WHERE killID = :killID", "source", array(":killID" => $killID));
    }

    /**
     * @param $killID
     *
     * @return null
     */
    public function getJSONByID($killID)
    {
        return $this->db->queryField("SELECT kill_json FROM killmails WHERE killID = :killID", "kill_json", array(":killID" => $killID));
    }

    /**
     * @param $killID
     * @param $processed
     * @param $hash
     * @param $source
     * @param $kill_json
     */
    public function insertKillmail($killID, $processed, $hash, $source, $kill_json)
    {
        if(!empty($killID) && !empty($processed) && !empty($hash) && !empty($source) && !empty($kill_json))
        {
            $this->db->execute("INSERT INTO killmails (killID, processed, hash, source, kill_json) VALUES (:killID, :processed, :hash, :source, :kill_json)", array(
                ":killID" => $killID,
                ":processed" => $processed,
                ":hash" => $hash,
                ":source" => $source,
                ":kill_json" => $kill_json
                ));
        }
    }
}