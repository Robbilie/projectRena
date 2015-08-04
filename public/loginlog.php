<?php

	require_once("../RenaBot");

	$loginRows = $app->Db->query("SELECT * FROM easLogs WHERE type = 'login' ORDER BY timestamp DESC");

	foreach ($loginRows as $loginRow) {
		$data = json_decode($loginRow['data'], true);
		$char = $app->CoreManager->getCharacter($data['characterID']);
		echo date("Y-m-d\TH:i:s\Z", $loginRow['timestamp']).' <a href="https://gate.eveonline.com/Profile/'.$char->getName().'">'.$char->getName().'</a><br>';
	}
