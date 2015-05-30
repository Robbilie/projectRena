<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\Lib\PhealLoader;

/**
 * Class Blueprints.
 */
class Blueprints
{
    /**
     * @var int
     */
    public $accessMask = 2;

    /**
     * @param $apiKey
     * @param $vCode
     * @param $characterID
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID)
    {
        $pheal = PhealLoader::loadPheal($apiKey, $vCode);
        $pheal->scope = 'Char';
        $result = $pheal->Blueprints(array('characterID' => $characterID))->toArray();

        return $result;
    }
}
