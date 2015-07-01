<?php
namespace ProjectRena\Lib;

use ProjectRena\RenaApp;

/**
 * Shapes the output and calls up twig, outputs json or outputs xml
 */
class out
{

    /**
     * The Slim Application
     */
    private $app;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
    }

    public function toTwig($templateFile, $dataArray = array(), $status = null)
    {
        // Generate character Information array data
        $characterInformation = array();
        if(isset($_SESSION["characterID"]))
        {
            $characterInformation = $this->app->characters->getAllByID($_SESSION["characterID"]);
            $characterInformation["corporation"] = $this->app->corporations->getAllByID($this->app->characters->getAllByID($_SESSION["characterID"])["corporationID"]);
            $characterInformation["alliance"] = $this->app->alliances->getAllByID($this->app->characters->getAllByID($_SESSION["characterID"])["allianceID"]);
            $characterInformation["groups"] = $this->app->UsersGroups->getGroup($this->app->Users->getUserByName($characterInformation["characterName"])["id"]);
        }

        $extraData = array(
            "loggedIn" => isset($_SESSION["loggedIn"]) ? true : false,
            "imageServer" => $this->app->baseConfig->getConfig("imageServer", "ccp"),
            "characterName" => isset($_SESSION["characterName"]) ? $_SESSION["characterName"] : null,
            "characterID" => isset($_SESSION["characterID"]) ? $_SESSION["characterID"] : null,
            "charInfo" => $characterInformation,
            "EVESSOURL" => $this->app->EVEOAuth->LoginURL()
        );

        $dataArray = array_merge($extraData, $dataArray);
        $this->app->render($templateFile, $dataArray, $status);
    }

    public function toJson($dataArray = array())
    {
        $this->app->contentType("application/javascript; charset=utf-8");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("X-Bin-Request-Count: 10000000");
        header("X-Bin-Max-Requests: 10000000");

        echo json_encode($dataArray, JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_UNESCAPED_SLASHES);
    }

    public function toXML($dataArray = array())
    {
        $this->app->contentType("application/xml");
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST");
        header("X-Bin-Request-Count: 10000000");
        header("X-Bin-Max-Requests: 10000000");

        $xml = new \SimpleXMLElement("<rena/>");
        array_walk_recursive($dataArray, array($xml, "addChild"));
        echo $xml->asXML();
    }

    public function RunAsNew(){}
}
