<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class MailsController
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

    public function getAllMails () {
        $mails = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $mails = $this->db->query(
                "SELECT * FROM ntMailMessage,ntMailMessageRecipient,(SELECT id,name,'P' as type FROM ntCharacter UNION SELECT id,name,'A' as type FROM ntAlliance UNION SELECT id,name,'C' as type FROM ntCorporation UNION SELECT id,name,'ML' as type FROM ntMailingList) recp
                WHERE ntMailMessage.messageID=ntMailMessageRecipient.messageID AND ntMailMessageRecipient.recipientID=recp.id AND ntMailMessageRecipient.recipientID IN (:characterID, :corporationID, :allianceID) ORDER BY ntMailMessage.sentDate DESC LIMIT 100",
                array(
                    ":characterID" => $character->getCharId(),
                    ":corporationID" => $character->getCorpId(),
                    ":allianceID" => $character->getAlliId()
                )
            );
            foreach ($mails as &$mail) {
                $mail['sentDate'] = date("Y-m-d\TH:i:s\Z", $mail['sentDate']);
                $mail['message'] = preg_replace('/(color="#)[a-f0-9]{2}([a-f0-9]{6}")/', '\1\2', preg_replace('/size="[^"]*[^"]"/', "", $mail['message']));
                $mail['senderName'] = $mail['type'] == "ML" ? "" : $this->app->CoreManager->getCharacter($mail['senderID'])->getCharName();
            }
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($mails));
    }

    public function getPersonalMails () {
        $mails = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $mails = $this->db->query(
                "SELECT * FROM ntMailMessage,ntMailMessageRecipient,(SELECT id,name,'A' as type FROM ntAlliance UNION SELECT id,name,'C' as type FROM ntCorporation UNION SELECT id,name,'ML' as type FROM ntMailingList) recp
                WHERE ntMailMessage.messageID=ntMailMessageRecipient.messageID AND ntMailMessageRecipient.recipientID=recp.id AND ntMailMessageRecipient.recipientID = :characterID ORDER BY ntMailMessage.sentDate DESC LIMIT 100",
                array(
                    ":characterID" => $character->getCharId()
                )
            );
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($mails));
    }

    public function getCorporationMails () {
        $mails = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $mails = $this->db->query(
                "SELECT * FROM ntMailMessage,ntMailMessageRecipient,(SELECT id,name,'A' as type FROM ntAlliance UNION SELECT id,name,'C' as type FROM ntCorporation UNION SELECT id,name,'ML' as type FROM ntMailingList) recp
                WHERE ntMailMessage.messageID=ntMailMessageRecipient.messageID AND ntMailMessageRecipient.recipientID=recp.id AND ntMailMessageRecipient.recipientID = :corporationID ORDER BY ntMailMessage.sentDate DESC LIMIT 100",
                array(
                    ":corporationID" => $character->getCorpId()
                )
            );
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($mails));
    }

    public function getAllianceMails () {
        $mails = array();
        if(isset($_SESSION["loggedIn"])) {
            $character = $this->app->CoreManager->getCharacter($_SESSION['characterID']);
            $mails = $this->db->query(
                "SELECT * FROM ntMailMessage,ntMailMessageRecipient,(SELECT id,name,'A' as type FROM ntAlliance UNION SELECT id,name,'C' as type FROM ntCorporation UNION SELECT id,name,'ML' as type FROM ntMailingList) recp
                WHERE ntMailMessage.messageID=ntMailMessageRecipient.messageID AND ntMailMessageRecipient.recipientID=recp.id AND ntMailMessageRecipient.recipientID = :allianceID ORDER BY ntMailMessage.sentDate DESC LIMIT 100",
                array(
                    ":allianceID" => $character->getAlliId()
                )
            );
        }
        $this->app->response->headers->set('Content-Type', 'application/json');
        $this->app->response->body(json_encode($mails));
    }

}
