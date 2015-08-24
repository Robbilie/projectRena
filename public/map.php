<?php

	require_once("../RenaBot");

	$regionID = isset($_REQUEST['region']) ? $app->mapRegions->getRegionIDbyName($_REQUEST['region']) : 10000018; // The Spire

	$systemsDB = $app->Db->query("SELECT solarSystemID, solarSystemName, x, y, z FROM mapSolarSystems WHERE regionID = :regionID", array(":regionID" => $regionID));

	$jumpsDB = $app->Db->query("SELECT * FROM mapSolarSystemJumps WHERE fromRegionID = :regionID OR toRegionID = :regionID", array(":regionID" => $regionID));

	$systems = array();
	foreach ($systemsDB as $system) {
		$systems[$system['solarSystemID']] = $system;
	}

	$jumps = array();
	foreach ($jumpsDB as $jump) {
		if(!isset($jumps[$jump['fromSolarSystemID']]))
			$jumps[$jump['fromSolarSystemID']] = array();
		$jumps[$jump['fromSolarSystemID']][] = $jump['toSolarSystemID'];
	}

	$minX = 0;
	$minZ = 0;
	$maxX = 0;
	$maxZ = 0;

	$dist = 100000;

	$grid = array();
	$gridInfo = array();
	foreach ($systems as $systemID => $system) {
		if(!isset($gridInfo[$systemID])) {
			$gridInfo[$systemID] = array("x" => 0, "z" => 0);
			$grid[0][0] = $systemID;
		}
		foreach ($jumps[$systemID] as $targetID) {
			if(!isset($systems[$targetID])) continue;
			if(isset($gridInfo[$targetID])) continue;

			$target = $systems[$targetID];

			$x = -1;
			$z = -1;

			$x = abs($system['x'] - $target['x']) < $dist ? 0 : ($target['x'] < $system['x'] ? -1 : 1);
			$z = abs($system['z'] - $target['z']) < $dist ? 0 : ($target['z'] < $system['z'] ? -1 : 1);

			$addX = 0;
			$addZ = 0;

			$found = false;

			for($outer = 1; $outer <= 10; $outer++) {
				$si = 0;
				if($outer == 1) {
					if($z == 0) {
						if($x == 1) {
							$si = 1;
						} else {
							$si = 3;
						}
					} else if($z == 1) {
						$si = 2;
					}
				}
				for($side = $si; $side < 4; $side++) {
					$st = 0;
					if($outer == 1) {
						switch ($side) {
							case 0:
								$st = $x + 1;
								break;
							case 1:
								$st = $z + 1;
								break;
							case 2:
								$st = abs($x - 1);
								break;
							case 3:
								$st = abs($z - 1);
								break;
						}
					}
					for($step = $st; $step < $outer * 2; $step++) {
						if(!isset($grid[$gridInfo[$systemID]['x'] + $x + $addX][$gridInfo[$systemID]['z'] + $z + $addZ])) {
							$found = true;
						} else {
							switch ($side) {
								case 0:
									$addX += 1;
									break;
								case 1:
									$addZ += 1;
									break;
								case 2:
									$addX -= 1;
									break;
								case 3:
									$addZ -= 1;
									if($step + 1 == $outer * 2) {
										$addZ -= 1;
										$addX -= 1;
									}
									break;
							}
						}
					}
					if($found) break;
				}
				if($found) break;
			}


			$grid[$gridInfo[$systemID]['x'] + $x + $addX][$gridInfo[$systemID]['z'] + $z + $addZ] = $targetID;
			$gridInfo[$targetID] = array("x" => $gridInfo[$systemID]['x'] + $x + $addX, "z" => $gridInfo[$systemID]['z'] + $z + $addZ);

		}
	}

	foreach ($gridInfo as $gridID => $gridData) {
		if($gridData["x"] < $minX)
			$minX = $gridData["x"];
		if($gridData["x"] > $maxX)
			$maxX = $gridData["x"];
		if($gridData["z"] < $minZ)
			$minZ = $gridData["z"];
		if($gridData["z"] > $maxZ)
			$maxZ = $gridData["z"];
	}

	$width = 100 / ($maxX - $minX + 1);
	$height = 100 / ($maxZ - $minZ + 1);
	//var_dump($minX);

?>
<!DOCTYPE html>
<html>
	<head>
		<title></title>
		<style type="text/css">
			body, html {
				height: 100%;
				width: 100%;
			}
			#grid {
				width: 100%;
				height: 100%;
			}
			#grid div {
				text-align: center;
				position: absolute;
				display: flex;
				flex-direction: column;
				justify-content: center;
			}
		</style>
	</head>
	<body>
		<div id="grid">
			<?php
				foreach ($gridInfo as $gridID => $gridData) {
					echo '<div style="width: '.$width.'%; height: '.$height.'%; left: '.(($gridData['x'] - $minX) * $width).'%; top: '.(($gridData['z'] - $minZ) * $height).'%;">'.$systems[$gridID]['solarSystemName'].'<br>['.$gridData['x'].'|'.$gridData['z'].']'.'</div>';
				}
			?>
		</div>
		<div id="debug">
			<pre><?php var_dump($gridInfo); ?></pre>
		</div>
	</body>
</html>