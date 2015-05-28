<?php


namespace ProjectRena\Model\EVEApi\EVE;

use ProjectRena\Lib\PhealLoader;

/**
 * Class FacWarStats
 *
 * @package ProjectRena\Model\EVEApi\EVE
 */
class FacWarStats {
	/**
	 * @var int
	 */
	public $accessMask = null;

	/**
	 * @return mixed
	 */
	public function getData()
	{
		$pheal = PhealLoader::loadPheal();
		$pheal->scope = "EVE";
		$result = $pheal->FacWarStats()->toArray();

		return $result;
	}
}