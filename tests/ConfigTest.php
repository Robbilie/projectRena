<?php
namespace ProjectRena;
require_once(__DIR__ . "/init.php");

/**
 * @backupGlobals disabled
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function __construct()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();
    }

    public function testSet()
    {
        $set = $this->app->Config->set("test", "test");
        $this->assertEquals(1, $set);
    }
    public function testGet()
    {
        $get = $this->app->Config->get("test");
        $this->assertEquals("test", $get);
    }

}