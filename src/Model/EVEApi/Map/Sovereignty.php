<?php

namespace ProjectRena\Model\EVEApi\Map;

use ProjectRena\RenaApp;

/**
 * Class Sovereignty.
 */
class Sovereignty
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
            $pheal->scope = 'Map';
            $result = $pheal->Sovereignty()->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
