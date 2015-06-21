<?php
namespace ProjectRena\Model\EVE;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class alliances
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
     * @param $allianceID
     *
     * @return array
     */
    public function getAllByID($allianceID)
    {
        return $this->db->queryRow("SELECT * FROM alliances WHERE allianceID = :id", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceName
     *
     * @return array
     */
    public function getAllByName($allianceName)
    {
        return $this->db->queryRow("SELECT * FROM alliances WHERE allianceName = :name", array(":name" => $allianceName), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return array
     */
    public function getAllByAllianceID($allianceID)
    {
        return $this->db->queryRow("SELECT * FROM alliances WHERE allianceID = :id", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return null
     */
    public function getInformationByID($allianceID)
    {
        return $this->db->queryField("SELECT history FROM alliances WHERE allianceID = :id", "history", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceName
     *
     * @return null
     */
    public function getInformationByName($allianceName)
    {
        return $this->db->queryField("SELECT history FROM alliances WHERE allianceName = :name", "history", array(":name" => $allianceName), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return null
     */
    public function getExecutorCorporationIDByID($allianceID)
    {
        return $this->db->queryField("SELECT executorCorporationID FROM alliances WHERE allianceID = :id", "executorCorporationID", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceName
     *
     * @return null
     */
    public function getExecutorCorporationIDByName($allianceName)
    {
        return $this->db->queryField("SELECT executorCorporationID FROM alliances WHERE allianceName = :name", "executorCorporationID", array(":name" => $allianceName), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return null
     */
    public function getAllianceTickerByID($allianceID)
    {
        return $this->db->queryField("SELECT allianceTicker FROM alliances WHERE allianceID = :id", "allianceTicker", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceName
     *
     * @return null
     */
    public function getAllianceTickerByName($allianceName)
    {
        return $this->db->queryField("SELECT allianceTicker FROM alliances WHERE allianceName = :name", "allianceTicker", array(":name" => $allianceName), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return null
     */
    public function getMemberCountByID($allianceID)
    {
        return $this->db->queryField("SELECT memberCount FROM alliances WHERE allianceID = :id", "memberCount", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $allianceName
     *
     * @return null
     */
    public function getMemberCountByName($allianceName)
    {
        return $this->db->queryField("SELECT memberCount FROM alliances WHERE allianceName = :name", "memberCount", array(":name" => $allianceName), 3600);
    }

    /**
     * @param null $allianceID
     * @param null $allianceName
     * @param null $allianceTicker
     * @param null $memberCount
     * @param null $executorCorporationID
     * @param null $information
     *
     * @internal param null $ceoID
     */
    public function updateAllianceDetails(
     $allianceID,
     $allianceName = null,
     $allianceTicker = null,
     $memberCount = null,
     $executorCorporationID = null,
     $information = null
    )
    {
        if(!empty($allianceID))
        {
            $exists = $this->getAllByID($allianceID);
            if($exists)
            {
                if($allianceName) $this->db->execute("UPDATE alliances SET allianceName = :allianceName WHERE allianceID = :allianceID", array(
                 "allianceName" => $allianceName,
                 ":allianceID"  => $allianceID,
                ));
                if($allianceTicker) $this->db->execute("UPDATE alliances SET allianceTicker = :allianceTicker WHERE allianceID = :allianceID", array(
                 ":allianceTicker" => $allianceTicker,
                 ":allianceID"  => $allianceID,
                ));
                if($memberCount) $this->db->execute("UPDATE alliances SET memberCount = :memberCount WHERE allianceID = :allianceID", array(
                 ":memberCount" => $memberCount,
                 ":allianceID"  => $allianceID,
                ));
                if($executorCorporationID) $this->db->execute("UPDATE alliances SET executorCorporationID = :executorCorporationID WHERE allianceID = :allianceID", array(
                 ":executorCorporationID" => $executorCorporationID,
                 ":allianceID"  => $allianceID,
                ));
                if($information) $this->db->execute("UPDATE alliances SET history = :history WHERE allianceID = :allianceID", array(
                 ":information"      => $information,
                 ":allianceID" => $allianceID,
                ));

            } elseif(!empty($allianceID) && !empty($allianceName) && !empty($allianceTicker) && !empty($memberCount) && !empty($executorCorporationID) && !empty($information))
            {
                $this->db->execute("INSERT INTO alliances (allianceID, allianceName, allianceTicker, memberCount, executorCorporationID, information) VALUES (:allianceID, :allianceName, :allianceTicker, :memberCount, :executorCorporationID, :information)", array(
                 ":allianceID"    => $allianceID,
                 ":allianceName" => $allianceName,
                 ":allianceTicker" => $allianceTicker,
                 ":memberCount" => $memberCount,
                 ":executorCorporationID" => $executorCorporationID,
                 ":information" => $information,
                ));
            }
        }
    }

    /**
     * @param $allianceID
     * @param $lastUpdated
     */
    public function setLastUpdated($allianceID, $lastUpdated)
    {
        if($lastUpdated)
            $this->db->execute("UPDATE alliances SET lastUpdated = :lastUpdated WHERE allianceID = :allianceID", array(":lastUpdated" => $lastUpdated, ":allianceID" => $allianceID));
    }

    /**
     * @param $allianceID
     * @param $executorCorporationID
     *
     */
    public function setExecutorCorporationID($allianceID, $executorCorporationID)
    {
        if($executorCorporationID)
            $this->db->execute("UPDATE alliances SET executorCorporationID = :executorCorporationID WHERE allianceID = :allianceID", array(":executorCorporationID" => $executorCorporationID, ":allianceID" => $allianceID));
    }

    /**
     * @param $allianceID
     * @param $memberCount
     */
    public function setMemberCount($allianceID, $memberCount)
    {
        if($memberCount)
            $this->db->execute("UPDATE alliances SET memberCount = :memberCount WHERE allianceID = :allianceID", array(":memberCount" => $memberCount, ":allianceID" => $allianceID));
    }
}