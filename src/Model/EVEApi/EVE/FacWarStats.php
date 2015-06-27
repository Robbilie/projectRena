<?php

namespace ProjectRena\Model\EVEApi\EVE;

use ProjectRena\RenaApp;

/**
 * Class FacWarStats.
 */
class FacWarStats
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
            $pheal->scope = 'EVE';
            $result = $pheal->FacWarStats()->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
