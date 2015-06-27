<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\RenaApp;

/**
 * Class Contracts.
 */
class Contracts
{
    /**
     * @var int
     */
    public $accessMask = 67108864;

    /**
     * @var
     */
    private $app;

    /**
     * @param \ProjectRena\RenaApp $app
     */
    function __construct(RenaApp $app)
    {
        $this->app = $app;
    }

    /**
     * @param $apiKey
     * @param $vCode
     * @param $characterID
     * @param null $contractID
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $contractID = null)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Char';
            $requestArray = array('characterID' => $characterID);
            if(isset($contractID))
            {
                $requestArray['contractID'] = $contractID;
            }
            $result = $pheal->Contracts($requestArray)->toArray();

            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, $characterID, $exception);
        }
    }
}
