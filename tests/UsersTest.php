<?php
namespace ProjectRena;

require_once(__DIR__ . "/init.php");

/**
 * @backupGlobals disabled
 */
class UsersTest extends \PHPUnit_Framework_TestCase
{
    private $app;
    private $userID;

    public function __construct()
    {
        $this->app = \ProjectRena\RenaApp::getInstance();
        $this->userID = 0;
    }

    public function testCreateUser()
    {
        $return = $this->app->Users->createUserWithCrest("test", 1, "asdf1234");
        if($return > 0) $return = true;

        $this->assertEquals(true, $return);
    }

    public function testGetUserByID()
    {
        $userID = $this->app->Users->createUserWithCrest("test", 1, "asdf1234");
        $data = $this->app->Users->getUserByID($userID);
        unset($data["id"]);
        unset($data["created"]);
        unset($data["updated"]);
        unset($data["loginHash"]);

        $this->assertArraySubset($data, array("characterName"      => "test",
                                              "characterID"        => "1",
                                              "characterOwnerHash" => "asdf1234",
                                              "accessToken"        => "",
                                              "refreshToken"       => "",
                                              "scopes"             => "",
                                              "tokenType"          => "",
        ));
    }

    public function testGetUserByName()
    {
        $data = $this->app->Users->getUserByName("test");
        unset($data["id"]);
        unset($data["created"]);
        unset($data["updated"]);
        unset($data["loginHash"]);
        $this->assertArraySubset($data, array("characterName"      => "test",
                                              "characterID"        => "1",
                                              "characterOwnerHash" => "asdf1234",
                                              "accessToken"        => "",
                                              "refreshToken"       => "",
                                              "scopes"              => "",
                                              "tokenType"          => "",
        ));
    }

    public function testSetUserLoginHash()
    {
        $userID = $this->app->Users->createUserWithCrest("test", 1, "asdf1234");
        $set = $this->app->Users->setUserAutoLoginHash($userID, "fdsa");
        $this->assertEquals(1, $set);
    }

    public function testGetUserDataByLoginHash()
    {
        $data = $this->app->Users->getUserDataByLoginHash("fdsa");
        unset($data["id"]);
        unset($data["created"]);
        unset($data["updated"]);

        $this->assertArraySubset($data, array("characterName"      => "test",
                                              "characterID"        => "1",
                                              "characterOwnerHash" => "asdf1234",
                                              "loginHash"          => "fdsa",
                                              "accessToken"        => "",
                                              "refreshToken"       => "",
                                              "scopes"              => "",
                                              "tokenType"          => "",
        ));
    }
}