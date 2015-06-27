<?php

namespace ProjectRena\Model\EVEApi\EVE;

use ProjectRena\RenaApp;

/**
 * Class CharacterAffiliation.
 */
class CharacterAffiliation
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
     * @param array $characterIDs
     *
     * @return mixed
     */
    public function getData($characterIDs = array())
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal();
            $pheal->scope = 'EVE';
            $result = $pheal->CharacterAffiliation(array('ids' => implode(',', $characterIDs)))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
