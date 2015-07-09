<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class GroupsController
{
    /**
     * @var RenaApp
     */
    protected $app;
    protected $db;
    protected $config;

    /**
     * @param RenaApp $app
     */
    public function __construct(RenaApp $app)
    {
        $this->app = $app;
        $this->db = $this->app->Db;
        $this->config = $this->app->baseConfig;
    }

    // get a special group
    public function getGroup ($groupID) {
        $group = array("group" => null, "canEdit" => false, "canAdd" => false);
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group['group'] = $this->app->CoreManager->getGroup((int)$groupID);
            $group['group']->getPermissions();
            $group['canEdit'] =
                $group['group']->getScope() == "corporation" ?
                    $char->hasPermission("writePermissionsGroupCorporation") :
                    $group['group']->getScope() == "alliance" ?
                        $char->hasPermission("writePermissionsGroupAlliance") :
                        false;
            $group['canAdd'] =
                $group['group']->getScope() == "corporation" ?
                    $char->hasPermission("writeMembersGroupCorporation") :
                    $group['group']->getScope() == "alliance" ?
                        $char->hasPermission("writeMembersGroupAlliance") :
                        false;
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($group));
    }

    // get availbale groups
    public function getGroups () {
        $groups = array("owned" => array(), "corporation" => array(), "alliance" => array(), "groups" => array());
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $groups['cancreate'] = $char->hasPermission("writeGroupCorporation") || $char->hasPermission("writeGroupAlliance");
            $groups['groups'] = $char->getGroups();
            // corp group if ceo and corp scoped groups
            if($char->getCCorporation()->getCeoCharacterId() == $char->getCharId()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getCorpId(), "corporation"));
            }
            // alliance group if alli ceo and alliance scoped groups
            if($char->getCCorporation()->getCAlliance()->getExecCorp()->getCeoCharacterId() == $char->getCharId()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getAlliId(), "alliance"));
            }
            // admin scoped groups if user admin
            if($char->getCUser()->isAdmin()) {
                $groups['owned'] = array_merge($groups['owned'], $this->app->CoreManager->getGroupsByOwnerAndScope(null, "admin"));
                //
                // TODO : maybe add owner IS NULL AND custom = 0
                //
            }
            $groups['corporation'] = array_merge($groups['corporation'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getCorpId(), "corporation"));
            $groups['alliance'] = array_merge($groups['alliance'], $this->app->CoreManager->getGroupsByOwnerAndScope($char->getAlliId(), "alliance"));
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($groups));
    }
    // return the members of a group
    public function getGroupMembers ($groupID) {
        $members = array();
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($group->getScope() == "corporation") {
              $corpID = $char->getCorpId();
              $members = $group->getCCharacters(function ($i) use ($corpID) { return $i->getCorpId() == $corpID; });
            } else if($group->getScope() == "alliance") {
              $alliID = $char->getAlliId();
              $members = $group->getCCharacters(function ($i) use ($alliID) { return $i->getAlliId() == $alliID; });
            } else {
              $members = $group->getCCharacters();
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($members));
    }

    // remove a permission from a group
    public function removePermissionFromGroup ($groupID, $permissionID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($group->hasPermission($permissionID)) {
                if(
                    (
                        $group->getScope() == "corporation" && $char->hasPermission("writePermissionsGroupCorporation")
                    ) ||
                    (
                        $group->getScope() == "alliance" && $char->hasPermission("writePermissionsGroupAlliance")
                    )
                ) {
                    $group->removePermission($permissionID);
                    $resp['state'] = "success";
                } else {
                    $resp['msg'] = "You are not permitted to do this.";
                }
            } else {
                $resp['msg'] = "Group does not have Permission.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // add a permission to a group
    public function addPermissionToGroup ($groupID, $permissionID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            $permission = $this->app->CoreManager->getPermission((int)$permissionID);
            if(
                (
                    $group->getScope() == "corporation" && $char->hasPermission("writePermissionsGroupCorporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writePermissionsGroupAlliance")
                )
            ) {
                $group->addPermission($permissionID);
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // remove a character from a group
    public function removeCharacterFromGroup ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $otherchar = $this->app->CoreManager->getCharacter($characterID);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if(
                (
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroupCorporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroupAlliance")
                )
            ) {
                if(in_array($group->getId(), $otherchar->getGroups())) {
                    $group->removeCharacter($otherchar->getCharId());
                    $resp['state'] = "success";
                } else {
                    $resp['msg'] = "Character not in Group.";
                }
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // add a character to a group
    public function addCharacterToGroup ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $otherchar = $this->app->CoreManager->getCharacter($characterID);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if(
                (
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroupCorporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroupAlliance")
                )
            ) {
                $group->addCharacter($otherchar->getCharId());
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // create a group
    public function createGroup ($name, $scope, $private) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if(
                (
                    $scope == "corporation" && $char->hasPermission("writeGroupCorporation")
                ) ||
                (
                    $scope == "alliance" && $char->hasPermission("writeGroupAlliance")
                )
            ) {
                if(trim($name) != "") {
                    if(!$this->app->CoreManager->entityExists($name)) {
                        $this->app->CoreManager->createGroup(
                            $name,
                            $scope,
                            (
                                $private ?
                                    (
                                        $scope == "corporation" ?
                                            $char->getCorpId() :
                                            (
                                                $scope == "alliance" ?
                                                    $char->getAlliId() :
                                                    null
                                            )
                                    ) :
                                    (
                                        $char->getCUser()->isAdmin() ?
                                            null :
                                            (
                                                $scope == "corporation" ?
                                                    $char->getCorpId() :
                                                    (
                                                        $scope == "alliance" ?
                                                            $char->getAlliId() :
                                                            null
                                                    )
                                            )
                                    )
                            ),
                            1
                        );
                        $resp['state'] = "success";
                    } else {
                        $resp['msg'] = "Name cannot be Corp/Alli Name.";
                    }
                } else {
                    $resp['msg'] = "Name cant be blank.";
                }
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

}
