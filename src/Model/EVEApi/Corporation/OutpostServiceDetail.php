<?php

namespace ProjectRena\Model\EVEApi\Corporation;

use ProjectRena\RenaApp;

/**
 * Class OutpostServiceDetail.
 */
class OutpostServiceDetail
{
    /**
     * @var int
     */
    public $accessMask = 32768;

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
     * @param $itemID
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $itemID)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Corp';
            $result = $pheal->OutpostServiceDetail(array(
                'characterID' => $characterID,
                'itemID'      => $itemID,
            ))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, $characterID, $exception);
        }
    }
}
