<?php
namespace ProjectRena\Model\CCP;

use ProjectRena\RenaApp;

/**
 * Class invTypes
 *
 * @package ProjectRena\Model\EVE
 */
class invTypes
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
     * @param $typeID
     *
     * @return null
     */
    public function getNameByID($typeID)
    {
        return $this->db->queryField("SELECT typeName FROM invTypes WHERE typeID = :id", "typeName", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getIDByName($typeName)
    {
        return $this->db->queryField("SELECT typeID FROM invTypes WHERE typeName = :name", "typeID", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return array
     */
    public function getAllByID($typeID)
    {
        return $this->db->queryRow("SELECT * FROM invTypes WHERE typeID = :id", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return array
     */
    public function getAllByName($typeName)
    {
        return $this->db->queryRow("SELECT * FROM invTypes WHERE typeName = :name", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getTypeIDByID($typeID)
    {
        return $this->db->queryField("SELECT typeID FROM invTypes WHERE typeID = :id", "typeID", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getTypeIDByName($typeName)
    {
        return $this->db->queryField("SELECT typeID FROM invTypes WHERE typeName = :name", "typeID", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getGroupIDByID($typeID)
    {
        return $this->db->queryField("SELECT groupID FROM invTypes WHERE typeID = :id", "groupID", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getGroupIDByName($typeName)
    {
        return $this->db->queryField("SELECT groupID FROM invTypes WHERE typeName = :name", "groupID", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getTypeNameByID($typeID)
    {
        return $this->db->queryField("SELECT typeName FROM invTypes WHERE typeID = :id", "typeName", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getTypeNameByName($typeName)
    {
        return $this->db->queryField("SELECT typeName FROM invTypes WHERE typeName = :name", "typeName", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getDescriptionByID($typeID)
    {
        return $this->db->queryField("SELECT description FROM invTypes WHERE typeID = :id", "description", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getDescriptionByName($typeName)
    {
        return $this->db->queryField("SELECT description FROM invTypes WHERE typeName = :name", "description", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getMassByID($typeID)
    {
        return $this->db->queryField("SELECT mass FROM invTypes WHERE typeID = :id", "mass", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getMassByName($typeName)
    {
        return $this->db->queryField("SELECT mass FROM invTypes WHERE typeName = :name", "mass", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getVolumeByID($typeID)
    {
        return $this->db->queryField("SELECT volume FROM invTypes WHERE typeID = :id", "volume", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getVolumeByName($typeName)
    {
        return $this->db->queryField("SELECT volume FROM invTypes WHERE typeName = :name", "volume", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getCapacityByID($typeID)
    {
        return $this->db->queryField("SELECT capacity FROM invTypes WHERE typeID = :id", "capacity", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getCapacityByName($typeName)
    {
        return $this->db->queryField("SELECT capacity FROM invTypes WHERE typeName = :name", "capacity", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getPortionSizeByID($typeID)
    {
        return $this->db->queryField("SELECT portionSize FROM invTypes WHERE typeID = :id", "portionSize", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getPortionSizeByName($typeName)
    {
        return $this->db->queryField("SELECT portionSize FROM invTypes WHERE typeName = :name", "portionSize", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getRaceIDByID($typeID)
    {
        return $this->db->queryField("SELECT raceID FROM invTypes WHERE typeID = :id", "raceID", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getRaceIDByName($typeName)
    {
        return $this->db->queryField("SELECT raceID FROM invTypes WHERE typeName = :name", "raceID", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getBasePriceByID($typeID)
    {
        return $this->db->queryField("SELECT basePrice FROM invTypes WHERE typeID = :id", "basePrice", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getBasePriceByName($typeName)
    {
        return $this->db->queryField("SELECT basePrice FROM invTypes WHERE typeName = :name", "basePrice", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getPublishedByID($typeID)
    {
        return $this->db->queryField("SELECT published FROM invTypes WHERE typeID = :id", "published", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getPublishedByName($typeName)
    {
        return $this->db->queryField("SELECT published FROM invTypes WHERE typeName = :name", "published", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getMarketGroupIDByID($typeID)
    {
        return $this->db->queryField("SELECT marketGroupID FROM invTypes WHERE typeID = :id", "marketGroupID", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getMarketGroupIDByName($typeName)
    {
        return $this->db->queryField("SELECT marketGroupID FROM invTypes WHERE typeName = :name", "marketGroupID", array(":name" => $typeName), 3600);
    }

    /**
     * @param $typeID
     *
     * @return null
     */
    public function getChanceOfDuplicatingByID($typeID)
    {
        return $this->db->queryField("SELECT chanceOfDuplicating FROM invTypes WHERE typeID = :id", "chanceOfDuplicating", array(":id" => $typeID), 3600);
    }

    /**
     * @param $typeName
     *
     * @return null
     */
    public function getChanceOfDuplicatingByName($typeName)
    {
        return $this->db->queryField("SELECT chanceOfDuplicating FROM invTypes WHERE typeName = :name", "chanceOfDuplicating", array(":name" => $typeName), 3600);
    }
}