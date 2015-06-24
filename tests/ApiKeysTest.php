<?php
namespace ProjectRena;
require_once(__DIR__ . "/init.php");

/**
 * @backupGlobals disabled
 */
class ApiKeysTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    public function __construct()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();

    }
    public function testApiKeys()
    {
        $insert = $this->app->ApiKeys->updateAPIKey("1", "1"); // 1
        $this->assertEquals(true, $insert);

        $get = $this->app->ApiKeys->getAPIKey("1");
        unset($get["id"]);
        unset($get["dateAdded"]);
        unset($get["lastValidation"]);
        $this->assertArraySubset($get, array("keyID" => 1, "vCode" => 1, "userID" => null, "errorCode" => 0, "accessMask" => 0));

        $update = $this->app->ApiKeys->updateAPIKey("1", "2", "3");
        $this->assertEquals(true, $update);

        $get2 = $this->app->ApiKeys->getAPIKey("1");
        unset($get2["id"]);
        unset($get2["dateAdded"]);
        unset($get2["lastValidation"]);
        $this->assertArraySubset($get2, array("keyID" => 1, "vCode" => 2, "userID" => 3, "errorCode" => 0, "accessMask" => 0));

        $delete = $this->app->ApiKeys->deleteAPIKey("1");
        $this->assertEquals(true, $delete);

        $get3 = $this->app->ApiKeys->getAPIKey("1");
        $this->assertArraySubset($get3, array());
    }
}
