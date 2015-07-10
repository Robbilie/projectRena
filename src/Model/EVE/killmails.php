<?php
namespace ProjectRena\Model\EVE;

use DateTime;
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
     *
     * @return bool|int|string
     */
    public function insertKillmail($killID, $processed = 0, $hash, $source, $kill_json)
    {
        return $this->db->execute("INSERT IGNORE INTO killmails (killID, processed, hash, source, kill_json) VALUES (:killID, :processed, :hash, :source, :kill_json)", array(
                ":killID"    => $killID,
                ":processed" => $processed,
                ":hash"      => $hash,
                ":source"    => $source,
                ":kill_json" => $kill_json,
            )
        );
    }

    public function generateFromCREST($crestData)
    {
        $killArray = array();
        $killArray["killID"] = (int) $crestData["killID"];

        // Fix the timestamp
        $killTime = $crestData["killmail"]["killTime"];
        if(stristr($crestData["killmail"]["killTime"], "."))
        {
            $date = DateTime::createFromFormat("Y.m.d H:i:s", $crestData["killmail"]["killTime"]);
            $killTime = $date->format("Y-m-d H:i:s");
        }

        $killArray["solarSystemID"] = (int) $crestData["killmail"]["solarSystem"]["id"];
        $killArray["killTime"] = (string) $killTime;
        $killArray["moonID"] = (int) @$crestData["killmail"]["moonID"] ? $crestData["killmail"]["moonID"] : 0;

        $killArray["victim"] = $this->getVictim($crestData["killmail"]["victim"]);
        $killArray["attackers"] = $this->getAttackers($crestData["killmail"]["attackers"]);
        $killArray["items"] = $this->getItems($crestData["killmail"]["victim"]["items"]);

        return $killArray;
    }

    private function getVictim($victim)
    {
        $victimData = array();
        $victimData["shipTypeID"] = (int) $victim["shipType"]["id"];
        $victimData["characterID"] = (int) @$victim["character"]["id"];
        $victimData["characterName"] = (string) @$victim["character"]["name"];
        $victimData["corporationID"] = (int) $victim["corporation"]["id"];
        $victimData["corporationName"] = (string) @$victim["corporation"]["name"];
        $victimData["allianceID"] = (int) @$victim["alliance"]["id"];
        $victimData["allianceName"] = (string) @$victim["alliance"]["name"];
        $victimData["factionID"] = (int) @$victim["faction"]["id"];
        $victimData["factionName"] = (string) @$victim["faction"]["name"];
        $victimData["damageTaken"] = (int) @$victim["damageTaken"];

        return $victimData;
    }

    private function getAttackers($attackers)
    {
        $aggressors = array();

        foreach($attackers as $attacker)
        {
            $aggressor = array();
            $aggressor["characterID"] = (int) @$attacker["character"]["id"];
            $aggressor["characterName"] = (string) @$attacker["character"]["name"];
            $aggressor["corporationID"] = (int) @$attacker["corporation"]["id"];
            $aggressor["corporationName"] = (string) @$attacker["corporation"]["name"];
            $aggressor["allianceID"] = (int) @$attacker["alliance"]["id"];
            $aggressor["allianceName"] = (string) @$attacker["alliance"]["name"];
            $aggressor["factionID"] = (int) @$attacker["faction"]["id"];
            $aggressor["factionName"] = (string) @$attacker["faction"]["name"];
            $aggressor["securityStatus"] = (float) @$attacker["securityStatus"];
            $aggressor["damageDone"] = (int) @$attacker["damageDone"];
            $aggressor["finalBlow"] = (int) @$attacker["finalBlow"];
            $aggressor["weaponTypeID"] = (int) @$attacker["weaponType"]["id"];
            $aggressor["shipTypeID"] = (int) @$attacker["shipType"]["id"];
            $aggressors[] = $aggressor;
        }

        return $aggressors;
    }

    private function getItems($items)
    {
        $itemsArray = array();
        foreach($items as $item)
        {
            $i = array();
            $i["typeID"] = (int) @$item["itemType"]["id"];
            $i["flag"] = (int) @$item["flag"];
            $i["qtyDropped"] = (int) @$item["quantityDropped"];
            $i["qtyDestroyed"] = (int) @$item["quantityDestroyed"];
            $i["singleton"] = (int) @$item["singleton"];
            if(isset($i["items"]))
                $i["items"] = $this->getItems($i["items"]);

            $itemsArray[] = $i;
        }

        return $itemsArray;
    }
}