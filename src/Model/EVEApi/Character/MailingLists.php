<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\Lib\PhealLoader;

/**
 * Class MailingLists.
 */
class MailingLists
{
    /**
     * @var int
     */
    public $accessMask = 1024;

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
        $result = $pheal->MailingLists(array('characterID' => $characterID))->toArray();

        return $result;
    }
}
