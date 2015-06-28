<?php

namespace ProjectRena\Model\EVEApi\Account;

use ProjectRena\RenaApp;

/**
 * Class APIKeyInfo.
 */
class APIKeyInfo
{
    /**
     * @var null
     */
    public $accessMask = null;

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
            $result = $pheal->APIKeyInfo()->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
            return array("errorCode" => $exception->getCode(), "errorMessage" => $exception->getMessage());
        }
    }
}
