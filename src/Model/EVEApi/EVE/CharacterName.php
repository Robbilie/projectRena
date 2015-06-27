<?php

namespace ProjectRena\Model\EVEApi\EVE;

use ProjectRena\RenaApp;

/**
 * Class CharacterName.
 */
class CharacterName
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
     * @param array $characterIDs Max 250 characterIDs
     *
     * @return mixed
     */
    public function getData($characterIDs = array())
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal();
            $pheal->scope = 'EVE';
            $result = $pheal->CharacterName(array('ids' => implode(',', $characterIDs)))->toArray();
            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException(null, null, $exception);
        }
    }
}
