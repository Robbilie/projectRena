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
                    ($char->hasPermission("writePermissionsGroup", "corporation")) :
                    ($group['group']->getScope() == "alliance" ?
                        $char->hasPermission("writePermissionsGroup", "alliance") :
                        false);
            $group['canAdd'] =
                $group['group']->getScope() == "corporation" ?
                    ($char->hasPermission("writeMembersGroup", "corporation")) :
                    ($group['group']->getScope() == "alliance" ?
                        $char->hasPermission("writeMembersGroup", "alliance") :
                        false);
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($group));
    }

    // get availbale groups
    public function getGroups () {
        $groups = array("owned" => array(), "corporation" => array(), "alliance" => array(), "groups" => array(), "cancreate" => array());
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            if($char->hasPermission("writeGroups", "corporation"))
              array_push($groups['cancreate'], "corporation");
            if($char->hasPermission("writeGroups", "alliance"))
              array_push($groups['cancreate'], "alliance");
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

    // return application of a group
    public function getGroupApplications ($groupID) {
      $applications = array();
      if(isset($_SESSION["loggedIn"])) {
          $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
          $group = $this->app->CoreManager->getGroup((int)$groupID);
          if(
              (
                  $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroup", "corporation")
              ) ||
              (
                  $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroup", "alliance")
              )
          ) {
              $apps = $group->getApplications();
              if($group->isPublic()) {
                foreach ($apps as $applier) {
                  if(
                    ($group->getScope() == "corporation" && $applier->getCorpId() == $char->getCorpId()) ||
                    ($group->getScope() == "alliance" && $applier->getAlliId() == $char->getAlliId())
                  ) {
                      array_push($applications, $applier);
                  }
                }
              } else {
                $applications = $apps;
              }
          }
      }
      $this->app->response->headers->set('Content-Type', 'application/json');
      $this->app->response->body(json_encode($applications));
    }

    // apply to join a group
    public function apply ($groupID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if($group->isCustom()) {
                if(
                    $group->isPublic() ||
                    ($group->getScope() == "corporation" && $group->getOwner() == $char->getCorpId()) ||
                    ($group->getScope() == "alliance" && $group->getOwner() == $char->getAlliId())
                ) {
                    $this->db->execute("INSERT INTO easGroupApplications (characterID, groupID) VALUES (:characterID, :groupID)", array(":characterID" => $char->getCharId(), ":groupID" => $groupID));
                    $resp['state'] = "success";
                } else {
                    $resp['msg'] = "You are not permitted to do this.";
                }
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // accept application
    public function acceptApplication ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if(
                (
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroup", "corporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroup", "alliance")
                )
            ) {
                $group->acceptApplication($characterID);
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
    }

    // reject application
    public function rejectApplication ($groupID, $characterID) {
        $resp = array("msg" => "", "state" => "error");
        if(isset($_SESSION["loggedIn"])) {
            $char = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $group = $this->app->CoreManager->getGroup((int)$groupID);
            if(
                (
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroup", "corporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroup", "alliance")
                )
            ) {
                $group->rejectApplication($characterID);
                $resp['state'] = "success";
            } else {
                $resp['msg'] = "You are not permitted to do this.";
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($resp));
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
                        $group->getScope() == "corporation" && $char->hasPermission("writePermissionsGroup", "corporation")
                    ) ||
                    (
                        $group->getScope() == "alliance" && $char->hasPermission("writePermissionsGroup", "alliance")
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
                    $group->getScope() == "corporation" && $char->hasPermission("writePermissionsGroup", "corporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writePermissionsGroup", "alliance")
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
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroup", "corporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroup", "alliance")
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
                    $group->getScope() == "corporation" && $char->hasPermission("writeMembersGroup", "corporation")
                ) ||
                (
                    $group->getScope() == "alliance" && $char->hasPermission("writeMembersGroup", "alliance")
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
                    $scope == "corporation" && $char->hasPermission("writeGroups", "corporation")
                ) ||
                (
                    $scope == "alliance" && $char->hasPermission("writeGroups", "alliance")
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
