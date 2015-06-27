<?php

namespace ProjectRena\Model\EVEApi\Corporation;

use ProjectRena\RenaApp;

/**
 * Class StarbaseDetail.
 */

/**
 * Class StarbaseDetail
 *
 * @package ProjectRena\Model\EVEApi\Corporation
 */
class StarbaseDetail
{
    /**
     * @var int
     */
    public $accessMask = 131072;

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
     * @param $itemID
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $itemID)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Corp';
            $result = $pheal->StarbaseDetail(array('itemID' => $itemID))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
        }
    }
}
