<?php
namespace ProjectRena\Model\EVE;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class corporations
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
     * @param $corporationID
     *
     * @return array
     */
    public function getAllByID($corporationID)
    {
        return $this->db->queryRow("SELECT * FROM corporations WHERE corporationID = :id", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $corporationName
     *
     * @return array
     */
    public function getAllByName($corporationName)
    {
        return $this->db->queryRow("SELECT * FROM corporations WHERE corporationName = :name", array(":name" => $corporationName), 3600);
    }

    /**
     * @param $allianceID
     *
     * @return array
     */
    public function getAllByAllianceID($allianceID)
    {
        return $this->db->queryRow("SELECT * FROM corporations WHERE allianceID = :id", array(":id" => $allianceID), 3600);
    }

    /**
     * @param $corporationID
     *
     * @return null
     */
    public function getInformationByID($corporationID)
    {
        return $this->db->queryField("SELECT history FROM corporations WHERE corporationID = :id", "history", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $corporationName
     *
     * @return null
     */
    public function getInformationByName($corporationName)
    {
        return $this->db->queryField("SELECT history FROM corporations WHERE corporationName = :name", "history", array(":name" => $corporationName), 3600);
    }

    /**
     * @param $corporationID
     *
     * @return null
     */
    public function getCEOIDByID($corporationID)
    {
        return $this->db->queryField("SELECT ceoID FROM corporations WHERE corporationID = :id", "ceoID", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $corporationName
     *
     * @return null
     */
    public function getCEOIDByName($corporationName)
    {
        return $this->db->queryField("SELECT ceoID FROM corporations WHERE corporationName = :name", "ceoID", array(":name" => $corporationName), 3600);
    }

    /**
     * @param $corporationID
     *
     * @return null
     */
    public function getCorpTickerByID($corporationID)
    {
        return $this->db->queryField("SELECT corpTicker FROM corporations WHERE corporationID = :id", "corpTicker", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $corporationName
     *
     * @return null
     */
    public function getCorpTickerByName($corporationName)
    {
        return $this->db->queryField("SELECT corpTicker FROM corporations WHERE corporationName = :name", "corpTicker", array(":name" => $corporationName), 3600);
    }

    /**
     * @param $corporationID
     *
     * @return null
     */
    public function getMemberCountByID($corporationID)
    {
        return $this->db->queryField("SELECT memberCount FROM corporations WHERE corporationID = :id", "memberCount", array(":id" => $corporationID), 3600);
    }

    /**
     * @param $corporationName
     *
     * @return null
     */
    public function getMemberCountByName($corporationName)
    {
        return $this->db->queryField("SELECT memberCount FROM corporations WHERE corporationName = :name", "memberCount", array(":name" => $corporationName), 3600);
    }

    /**
     * @param $corporationID
     * @param null $allianceID
     * @param null $corporationName
     * @param null $ceoID
     * @param null $corpTicker
     * @param null $memberCount
     * @param null $information
     */
    public function updateCorporationDetails(
     $corporationID,
     $allianceID = null,
     $corporationName = null,
     $ceoID = null,
     $corpTicker = null,
     $memberCount = null,
     $information = null
    )
    {
        if(!empty($corporationID))
        {
            $exists = $this->getAllByID($corporationID);
            if($exists)
            {
                if($allianceID) $this->db->execute("UPDATE corporations SET allianceID = :allianceID WHERE corporationID = :corporationID", array(
                 "allianceID"   => $allianceID,
                 ":corporationID" => $corporationID,
                ));
                if($corporationName) $this->db->execute("UPDATE corporations SET corporationName = :corporationName WHERE corporationID = :corporationID", array(
                 "corporationName" => $corporationName,
                 ":corporationID"  => $corporationID,
                ));
                if($ceoID) $this->db->execute("UPDATE corporations SET ceoID = :ceoID WHERE corporationID = :corporationID", array(
                 ":ceoID" => $ceoID,
                 ":corporationID"  => $corporationID,
                ));
                if($corpTicker) $this->db->execute("UPDATE corporations SET corpTicker = :corpTicker WHERE corporationID = :corporationID", array(
                 ":corpTicker" => $corpTicker,
                 ":corporationID"  => $corporationID,
                ));
                if($memberCount) $this->db->execute("UPDATE corporations SET memberCount = :memberCount WHERE corporationID = :corporationID", array(
                 ":memberCount" => $memberCount,
                 ":corporationID"  => $corporationID,
                ));
                if($information) $this->db->execute("UPDATE corporations SET history = :history WHERE corporationID = :corporationID", array(
                 ":information"      => $information,
                 ":corporationID" => $corporationID,
                ));

            } elseif(!empty($corporationID) && !empty($allianceID) && !empty($corporationName) && !empty($ceoID) && !empty($corpTicker) && !empty($memberCount) && !empty($information))
            {
                $this->db->execute("INSERT INTO corporations (corporationID, allianceID, corporationName, ceoID, corpTicker, memberCount, information) VALUES (:corporationID, :allianceID, :corporationName, :ceoID, :corpTicker, :memberCount, :information)", array(
                 ":corporationID" => $corporationID,
                 ":allianceID"    => $allianceID,
                 ":corporationName" => $corporationName,
                 ":ceoID" => $ceoID,
                 ":corpTicker" => $corpTicker,
                 ":memberCount" => $memberCount,
                 ":information" => $information,
                ));
            }
        }
    }

    /**
     * @param $corporationID
     * @param $lastUpdated
     */
    public function setLastUpdated($corporationID, $lastUpdated)
    {
        if($lastUpdated)
            $this->db->execute("UPDATE corporations SET lastUpdated = :lastUpdated WHERE corporationID = :corporationID", array(":lastUpdated" => $lastUpdated, ":corporationID" => $corporationID));
    }

    /**
     * @param $corporationID
     * @param $ceoID
     */
    public function setCEOID($corporationID, $ceoID)
    {
        if($ceoID)
            $this->db->execute("UPDATE corporations SET ceoID = :ceoID WHERE corporationID = :corporationID", array(":ceoID" => $ceoID, ":corporationID" => $corporationID));
    }

    /**
     * @param $corporationID
     * @param $memberCount
     */
    public function setMemberCount($corporationID, $memberCount)
    {
        if($memberCount)
            $this->db->execute("UPDATE corporations SET memberCount = :memberCount WHERE corporationID = :corporationID", array(":memberCount" => $memberCount, ":corporationID" => $corporationID));
    }
}