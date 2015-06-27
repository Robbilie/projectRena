<?php

namespace ProjectRena\Model\EVEApi\EVE;

use ProjectRena\RenaApp;

/**
 * Class CharacterID.
 */
class CharacterID
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
     * @param array $characterNames
     *
     * @return mixed
     */
    public function getData($characterNames = array())
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal();
            $pheal->scope = 'EVE';
            $result = $pheal->CharacterID(array('names' => implode(',', $characterNames)))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
