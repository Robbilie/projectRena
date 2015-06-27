<?php

namespace ProjectRena\Model\EVEApi\Character;

use ProjectRena\RenaApp;

/**
 * Class NotificationTexts.
 */
class NotificationTexts
{
    /**
     * @var int
     */
    public $accessMask = 32768;

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
     * @param array $ids
     *
     * @return mixed
     */
    public function getData($apiKey, $vCode, $characterID, $ids = array())
    {
        try
        {
            $pheal = $this->app->Pheal->Pheal($apiKey, $vCode);
            $pheal->scope = 'Char';
            $result = $pheal->NotificationTexts(array(
                'characterID' => $characterID,
                'IDs'         => implode(',', $ids),
            ))->toArray();

            return $result;
        } catch(\Exception $exception)
        {
            $this->app->Pheal->handleApiException($apiKey, null, $exception);
        }
    }
}
