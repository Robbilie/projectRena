<?php
namespace ProjectRena;
require_once(__DIR__ . "/init.php");

/**
 * @backupGlobals disabled
 */
class UsersGroupsTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $userID;
    public function __construct()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();
        $this->userID = $this->app->Users->createUserWithCrest("test", 1, "asdf1234");
    }

    public function testSetGroup()
    {
        $set = $this->app->UsersGroups->setGroup($this->userID, 1);
        $this->assertEquals(1, $set);
    }
    public function testGetGroup()
    {
        $data = $this->app->UsersGroups->getGroup($this->userID);
        $this->assertArraySubset($data, array(1));

    }
    public function testDeleteGroup()
    {
        $delete = $this->app->UsersGroups->deleteGroup($this->userID, 1);
        $this->assertEquals(1, $delete);
    }
}