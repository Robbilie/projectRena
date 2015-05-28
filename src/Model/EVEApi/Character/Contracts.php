<?php


namespace ProjectRena\Model\EVEApi\Character;


use ProjectRena\Lib\PhealLoader;

/**
 * Class Contracts
 *
 * @package ProjectRena\Model\EVEApi\Character
 */
class Contracts {
	/**
	 * @var int
	 */
	public static $accessMask = 67108864;

	/**
	 * @param $apiKey
	 * @param $vCode
	 *
	 * @param $characterID
	 * @param null $contractID
	 *
	 * @return mixed
	 */
	public static function getData($apiKey, $vCode, $characterID, $contractID = null)
	{
		$pheal = PhealLoader::loadPheal($apiKey, $vCode);
		$pheal->scope = "Char";
		$requestArray = array("characterID" => $characterID);
		if(isset($contractID))
			$requestArray["contractID"] = $contractID;
		$result = $pheal->Contracts($requestArray)->toArray();

		return $result;
	}

}