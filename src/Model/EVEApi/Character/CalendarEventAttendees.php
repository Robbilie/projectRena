<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\RenaApp;

/**
 * Class CalendarEventAttendees.
 */
class CalendarEventAttendees
{
    /**
     * @var int
     */
    public $accessMask = 4;

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
     * @param $characterID
     * @param array $eventIDs
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $eventIDs = array())
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Char';
            $result = $pheal->CalendarEventAttendees(array(
                'characterID' => $characterID,
                'eventIDs'    => implode(',', $eventIDs),
            ))->toArray();

            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, $characterID, $exception);
        }
    }
}
