<?php

	require_once("../RenaBot");

	$marketGroupRows = $app->Db->query("SELECT * FROM invMarketGroups ORDER BY parentGroupID ASC");


	function findAndAppend (&$groups, $groupRow) {
		foreach ($groups as $key => &$value) {
			if($groupRow['parentGroupID'] == $key) {
				$value['subGroups'][$groupRow['marketGroupID']] = $groupRow;
			} else {
				findAndAppend($value['subGroups'], $groupRow);
			}
		}
	}

	$groups = array();
	foreach ($marketGroupRows as &$marketGroupRow) {
		$marketGroupRow['subGroups'] = array();
		if(is_null($marketGroupRow['parentGroupID'])) {
			$groups[$marketGroupRow['marketGroupID']] = $marketGroupRow;
		} else {
			if(isset($groups[$marketGroupRow['parentGroupID']])) {
				$groups[$marketGroupRow['parentGroupID']]['subGroups'][$marketGroupRow['marketGroupID']] = $marketGroupRow;
			} else {
				findAndAppend($groups, $marketGroupRow);
			}
		}
	}

	function renderGroups ($groups, $prefix) {
		foreach ($groups as $group) {
			echo $prefix.' '.$group['marketGroupName'].'<br>';
			renderGroups($group['subGroups'], $prefix.'- ');
		}
	}

	renderGroups($groups,"- ");