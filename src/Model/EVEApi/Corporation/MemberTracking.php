<?php

namespace ProjectRena\Model\EVEApi\Corporation;

use ProjectRena\RenaApp;

/**
 * Class MemberTracking
 *
 * @package ProjectRena\Model\EVEApi\Corporation
 */
class MemberTracking
{
    /**
     * @var int
     */
    public $accessMask = 2048;

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
     * @param int $extended
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $extended = 0)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Corp';
            $result = $pheal->MemberTracking(array('extended' => $extended))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
        }
    }
}
