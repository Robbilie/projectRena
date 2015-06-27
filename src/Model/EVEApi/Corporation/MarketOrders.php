<?php

namespace ProjectRena\Model\EVEApi\Corporation;

use ProjectRena\RenaApp;

/**
 * Class MarketOrders.
 */
class MarketOrders
{
    /**
     * @var int
     */
    public $accessMask = 4096;

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
     * @param null $orderID
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $orderID = null)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Corp';
            $requestArray = array('characterID' => $characterID);
            if(isset($orderID))
            {
                $requestArray['orderID'] = $orderID;
            }
            $result = $pheal->MarketOrders($requestArray)->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, $characterID, $exception);
        }
    }
}
