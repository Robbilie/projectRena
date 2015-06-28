<?php

namespace ProjectRena\Controller;

use ProjectRena\RenaApp;

class FetcherController
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

    public function postApiFetch () {
      $this->convertNotifications();
    }

    function convertNotifications () {
      $notificationRows = $this->db->query(
        "SELECT DISTINCT ntNotification.notificationID as distID, ntNotification.*, ntNotificationRecipient.recipientID
        FROM ntNotification LEFT JOIN easNotifications ON ntNotification.notificationID = easNotifications.eveID, ntNotificationRecipient
        WHERE ntNotification.notificationID = ntNotificationRecipient.notificationID
        AND easNotifications.eveID IS NULL
        GROUP BY ntNotification.notificationID",
        array()
      );
      foreach ($notificationRows as $notificationRow) {
        $recipient = $this->app->CoreManager->getCharacter($notificationRow['recipientID']);
        $this->db->execute(
          "INSER INTO easNotifications (eveID, state, typeID, creatorID, recipientID, locationID, body, created, requested) VALUES (:eveID, :state, :typeID, :creatorID, :recipientID, :locationID, :body, :created, :requested)",
          array(
            ":eveID" => $notificationRow['notificationID'],
            ":state" => 0,
            ":typeID" => $notificationRow['typeID'],
            ":creatorID" => $notificationRow['senderID'],
            ":recipientID" => $recipient->getCorpId(),
            ":locationID" => 0,
            ":body" => $notificationRow['body'],
            ":created" => $notificationRow['sentDate'],
            ":requested" => $notificationRow['sentDate']
          )
        );
      }
    }
}
