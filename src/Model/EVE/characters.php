<?php
namespace ProjectRena\Model\EVE;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class characters
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
     * @param $characterID
     *
     * @return array
     */
    public function getAllByID($characterID)
    {
        return $this->db->queryRow("SELECT * FROM characters WHERE characterID = :id", array(":id" => $characterID), 3600);
    }

    /**
     * @param $characterName
     *
     * @return array
     */
    public function getAllByName($characterName)
    {
        return $this->db->queryRow("SELECT * FROM characters WHERE characterName = :name", array(":name" => $characterName), 3600);
    }

    /**
     * @param $corporationID
     *
     * @return array
     */
    public function getAllByCorporationID($corporationID)
    {
        return $this->db->queryRow("SELECT * FROM characters WHERE corporationID = :id", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return array
     */
    public function getAllByAllianceID($allianceID)
    {
        return $this->db->queryRow("SELECT * FROM characters WHERE allianceID = :id", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $characterID
     *
     * @return null
     */
    public function getHistoryByID($characterID)
    {
        return $this->db->queryField("SELECT history FROM characters WHERE characterID = :id", "history", array(":id" => $characterID), 3600);
    }

    /**
     * @param $characterName
     *
     * @return null
     */
    public function getHistoryByName($characterName)
    {
        return $this->db->queryField("SELECT history FROM characters WHERE characterName = :name", "history", array(":name" => $characterName), 3600);
    }

    /**
     * @param $characterID
     * @param null $corporationID
     * @param null $allianceID
     * @param null $characterName
     * @param string $history
     */
    public function updateCharacterDetails(
     $characterID,
     $corporationID = null,
     $allianceID = null,
     $characterName = null,
     $history = null
    )
    {
        if(!empty($characterID))
        {
            $exists = $this->getAllByID($characterID);
            if($exists)
            {
                if($corporationID) $this->db->execute("UPDATE characters SET corporationID = :corporationID WHERE characterID = :characterID", array(
                 "corporationID" => $corporationID,
                 ":characterID"  => $characterID,
                ));
                if($allianceID) $this->db->execute("UPDATE characters SET allianceID = :allianceID WHERE characterID = :characterID", array(
                 "allianceID"   => $allianceID,
                 ":characterID" => $characterID,
                ));
                if($characterName) $this->db->execute("UPDATE characters SET characterName = :characterName WHERE characterID = :characterID", array(
                 "characterName" => $characterName,
                 ":characterID"  => $characterID,
                ));
                if($history) $this->db->execute("UPDATE characters SET history = :history WHERE characterID = :characterID", array(
                 "history"      => $history,
                 ":characterID" => $characterID,
                ));

            } elseif(!empty($characterID) && !empty($corporationID) && !empty($allianceID) && !empty($characterName) && !empty($history))
            {
                $this->db->execute("INSERT INTO characters (characterID, corporationID, allianceID, characterName, history) VALUES (:characterID, :corporationID, :allianceID, :characterName, :history)", array(
                  ":characterID"   => $characterID,
                  ":corporationID" => $corporationID,
                  ":allianceID"    => $allianceID,
                  ":characterName" => $characterName,
                  ":history"       => $history,
                 ));
            }
        }
    }

    /**
     * @param string $lastUpdated
     */
    public function setLastUpdated($characterID, $lastUpdated)
    {
        if($lastUpdated)
            $this->db->execute("UPDATE characters SET lastUpdated = :lastUpdated WHERE characterID = :characterID", array(":lastUpdated" => $lastUpdated, ":characterID" => $characterID));
    }
}