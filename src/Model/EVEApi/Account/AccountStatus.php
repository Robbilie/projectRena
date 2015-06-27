<?php

namespace ProjectRena\Model\EVEApi\Account;

use ProjectRena\RenaApp;

/**
 * Class AccountStatus.
 */
class AccountStatus
{
    /**
     * @var int
     */
    public $accessMask = 33554432;

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
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode)
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Account';
            $result = $pheal->accountStatus()->toArray();
            return $result;

        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
        }
    }
}
