<?php
namespace ProjectRena;
require_once(__DIR__ . "/init.php");

/**
 * @backupGlobals disabled
 */
class PasteTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function __construct()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();
    }

    public function testCreatePaste()
    {
        $create = $this->app->Paste->createPaste("asdf", 1, "asdf", 0);
        $this->assertEquals(1, $create);
    }

    public function testGetPasteData()
    {
        $get = $this->app->Paste->getPasteData("asdf");
        unset($get["timeout"]);

        $this->assertArraySubset($get, array("hash" => "asdf", "userID" => 1, "data" => "asdf"));
    }

    public function testDeletePaste()
    {
        $delete = $this->app->Paste->deletePaste("asdf");
        $this->assertEquals(1, $delete);
    }
}