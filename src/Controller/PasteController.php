<?php
namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

/**
 * Class PasteController
 *
 * @package ProjectRena\Controller
 */
class PasteController
{
    /**
     * @var RenaApp
     */
    protected $app;

    /**
     * @param RenaApp $app
     */
    function __construct(RenaApp $app)
    {
        $this->app = $app;
    }

    /**
     *
     */
    public function pastePage()
    {
        $this->app->out->toTwig('/paste/paste.twig');
    }

    /**
     *
     */
    public function postPaste()
    {
        $postData = $this->app->request->post("paste");
        $timeout = $this->app->request->post("timeout");
        if(isset($_SESSION["characterName"]))
        {
            $user = $this->app->Users->getUserByName($_SESSION["characterName"]);
            $userID = $user["id"];
            $hash = md5(time() . $_SESSION["characterName"] . $_SESSION["characterID"]);
            $this->app->paste->createPaste($hash, $userID, $postData, $timeout);
            $this->app->redirect("/paste/{$hash}/");
        } else
        {
            $this->app->out->toTwig("/paste/paste.twig", array("error" => "Error, you need to be logged in to post data"));
        }
    }

    /**
     * @param $hash
     */
    public function showPaste($hash)
    {
        $data = $this->app->Paste->getPasteData($hash);
        $pasteData = $data["data"];
        $pasteCreated = $data["created"];
        $userID = $data["userID"]; // attach user data for the user that created it
        $this->app->out->toTwig("/paste/pasteShow.twig", array("pasteData" => $pasteData, "pasteCreated" => $pasteCreated));
    }
}