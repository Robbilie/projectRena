<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\RenaApp;

/**
 * Class WalletJournal.
 */
class WalletJournal
{
    /**
     * @var int
     */
    public $accessMask = 2097152;

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
     * @param int $accountKey
     * @param null $fromID
     * @param null $rowCount
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $accountKey = 1000, $fromID = null, $rowCount = null)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Char';
            $requestArray = array('characterID' => $characterID, 'accountKey' => $accountKey);
            if(isset($fromID))
            {
                $requestArray['fromID'] = $fromID;
            }
            if(isset($rowCount))
            {
                $requestArray['rowCount'] = $rowCount;
            }

            $result = $pheal->WalletJournal($requestArray)->toArray();

            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
        }
    }
}
