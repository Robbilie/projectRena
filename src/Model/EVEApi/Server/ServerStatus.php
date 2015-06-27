<?php

namespace ProjectRena\Model\EVEApi\Server;

use ProjectRena\RenaApp;

/**
 * Class ServerStatus.
 */
class ServerStatus
{
    /**
     * @var int
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
     * @return mixed
     */
    public function getData()
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal();
            $pheal->scope = 'Server';
            $result = $pheal->ServerStatus()->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
