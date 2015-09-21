<?php

	require_once("../RenaBot");

	$regionID = isset($_REQUEST['region']) ? $app->mapRegions->getRegionIDbyName($_REQUEST['region']) : 10000018; // The Spire

	$systemsDB = $app->Db->query("SELECT solarSystemID, solarSystemName, x, y, z FROM mapSolarSystems WHERE regionID = :regionID", array(":regionID" => $regionID));

	$jumpsDB = $app->Db->query("SELECT * FROM mapSolarSystemJumps WHERE fromRegionID = :regionID OR toRegionID = :regionID", array(":regionID" => $regionID));

	$center = $app->Db->queryField("SELECT mapSolarSystems.solarSystemID, SQRT(POW(mapSolarSystems.x - mapRegions.x, 2) + POW(mapSolarSystems.y - mapRegions.y, 2) + POW(mapSolarSystems.z - mapRegions.z, 2)) as dis FROM mapSolarSystems LEFT JOIN mapRegions ON mapSolarSystems.regionID = mapRegions.regionID WHERE mapRegions.regionID = :regionID ORDER BY dis ASC LIMIT 1", "solarSystemID", array(":regionID" => $regionID));


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

	function norm ($vec) {
	    $norm = 0;
	    $components = count($vec);

	    for ($i = 0; $i < $components; $i++)
	        $norm += $vec[$i] * $vec[$i];

	    return sqrt($norm);
	}

	function dot ($vec1, $vec2) {
	    $prod = 0;
	    $components = count($vec1);

	    for ($i = 0; $i < $components; $i++)
	        $prod += ($vec1[$i] * $vec2[$i]);

	    return $prod;
	}

	function rad2cors ($rad) {
		$deg = rad2deg($rad);
		$cors = array(0,0);

		if($deg > 292.5 || $deg < 67.5) {
			$cors[1] = -1;
		} else if($deg > 112.5 && $deg < 247.5) {
			$cors[1] =  1;
		} else {
			$cors[1] =  0;
		}

		if($deg > 22.5 && $deg < 157.5) {
			$cors[0] =  1;
		} else if($deg > 202.5 && $deg < 337.5) {
			$cors[0] = -1;
		} else {
			$cors[0] =  0;
		}

		return $cors;
	}

	function setNeighbours ($systemID) {
		global $jumps, $systems, $gridInfo, $grid;
		foreach ($jumps[$systemID] as $toSystemID) {
			if(!isset($systems[$toSystemID])) continue;

			$v1 = array(0, 1);
			$v2 = array($systems[$toSystemID]['x'] - $systems[$systemID]['x'], $systems[$toSystemID]['z'] - $systems[$systemID]['z']);

			$ang = acos(dot($v1, $v2) / (norm($v1) * norm($v2)));

			$cors = rad2cors($ang);

			if(!isset($gridInfo[$toSystemID])) {
				$gridInfo[$toSystemID] = array("name" => $systems[$toSystemID]['solarSystemName'], "x" => $gridInfo[$systemID]["x"] + $cors[0], "z" => $gridInfo[$systemID]["z"] + $cors[1]);
				$grid[$gridInfo[$systemID]["x"] + $cors[0]][$gridInfo[$systemID]["z"] + $cors[1]][] = $toSystemID;
				setNeighbours($toSystemID);
			} else {
				if(!isset($_REQUEST['reverse'])) {
					$ind = array_search($toSystemID, $grid[$gridInfo[$toSystemID]["x"]][$gridInfo[$toSystemID]["z"]]);
					if($ind === FALSE) continue;
					unset($grid[$gridInfo[$toSystemID]["x"]][$gridInfo[$toSystemID]["z"]][$ind]);

					$gridInfo[$toSystemID]["x"] = $gridInfo[$toSystemID]["x"] + $cors[0];
					$gridInfo[$toSystemID]["z"] = $gridInfo[$toSystemID]["z"] + $cors[1];

					$grid[$gridInfo[$toSystemID]["x"]][$gridInfo[$toSystemID]["z"]][] = $toSystemID;
				}
			}

		}
	}

	if(!isset($gridInfo[$center])) {
		$gridInfo[$center] = array("name" => $systems[$center]['solarSystemName'], "x" => 0, "z" => 0);
		$grid[0][0][] = $center;
	}

	setNeighbours($center);

	$arrsLeft = true;
	$run = 0;

	while($arrsLeft && $run < 5000) {
		$run++;
		$arrsLeft = false;

		foreach ($grid as $x => $valX) {
			foreach ($valX as $z => $valZ) {
				if(count($grid[$x][$z]) > 1) {
					$arrsLeft = true;
					if($run == 4000)
						var_dump($grid[$x][$z]);

					$sumX = 0;
					$sumZ = 0;
					foreach ($grid[$x][$z] as $sysID) {
						$sumX += $systems[$sysID]["x"];
						$sumZ += $systems[$sysID]["z"];
					}

					$sumX /= count($grid[$x][$z]);
					$sumZ /= count($grid[$x][$z]);

					$tmpDis = null;
					$tmpID = null;
					foreach ($grid[$x][$z] as $sysID) {
						$tD = norm([$systems[$sysID]["x"] - $sumX, $systems[$sysID]["z"] - $sumZ]);
						if(is_null($tmpDis) || $tD < $tmpDis) {
							$tmpDis = $tD;
							$tmpID = $sysID;
						}
					}

					foreach ($grid[$x][$z] as $sInd => $sysID) {
						if($sysID == $tmpID) continue;

						$v1 = array(0, 1);
						$v2 = array($systems[$sysID]['x'] - $systems[$tmpID]['x'], $systems[$sysID]['z'] - $systems[$tmpID]['z']);

						$ang = acos(dot($v1, $v2) / (norm($v1) * norm($v2)));

						$cors = rad2cors($ang);

						$gridInfo[$sysID]["x"] = $gridInfo[$tmpID]["x"] + $cors[0];
						$gridInfo[$sysID]["z"] = $gridInfo[$tmpID]["z"] + $cors[1];


						unset($grid[$x][$z][$sInd]);
						$grid[$gridInfo[$tmpID]["x"] + $cors[0]][$gridInfo[$tmpID]["z"] + $cors[1]][] = $sysID;


					}



				}
			}
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
		<canvas id="canvas" width="1920" height="1080"></canvas>
		<!--<div id="grid">
			<?php
				foreach ($gridInfo as $gridID => $gridData) {
					echo '<div style="width: '.$width.'%; height: '.$height.'%; left: '.(($gridData['x'] - $minX) * $width).'%; top: '.(($gridData['z'] - $minZ) * $height).'%;">'.$systems[$gridID]['solarSystemName'].'<br>['.$gridData['x'].'|'.$gridData['z'].']'.'</div>';
				}
			?>
		</div>
		<div id="debug">
			<pre><?php var_dump($gridInfo); ?></pre>
		</div>-->
		<script type="text/javascript">
			var disX = <?php echo $width; ?> / 100 * 1820;
			var disZ = <?php echo $height; ?> / 100 * 980;
			var minX = <?php echo $minX; ?>;
			var minZ = <?php echo $minZ; ?>;
			var maxX = <?php echo $maxX; ?>;
			var maxZ = <?php echo $maxZ; ?>;

			var systems = <?php echo json_encode($gridInfo); ?>;
			var jumps = <?php echo json_encode($jumps); ?>;

			var canvas = document.getElementById("canvas");
			var ctx = canvas.getContext("2d");

			ctx.textAlign = "center";

			for(var fromSysID in jumps) {
				for(var i = 0; i < jumps[fromSysID].length; i++) {
					if(!systems[fromSysID]) continue;
					if(!systems[jumps[fromSysID][i]]) continue;
					if(fromSysID < jumps[fromSysID][i]) continue;
					ctx.beginPath();
					ctx.moveTo((systems[fromSysID]['x'] - minX) * disX + 50, canvas.height - (systems[fromSysID]['z'] - minZ) * disZ - 50);
					ctx.lineTo((systems[jumps[fromSysID][i]]['x'] - minX) * disX + 50, canvas.height - (systems[jumps[fromSysID][i]]['z'] - minZ) * disZ - 50);
					ctx.stroke();
					ctx.closePath();
				}
			}


			for(var systemID in systems) {
				ctx.beginPath();
				ctx.fillStyle = "white";
				ctx.strokeStyle = "black";
				ctx.fillRect((systems[systemID]['x'] - minX) * disX + 20, canvas.height - 62.5 - (systems[systemID]['z'] - minZ) * disZ, 60, 25);
				ctx.strokeRect((systems[systemID]['x'] - minX) * disX + 20, canvas.height - 62.5 - (systems[systemID]['z'] - minZ) * disZ, 60, 25);

				ctx.fillStyle = "black";
				ctx.fillText(systems[systemID]['name'], (systems[systemID]['x'] - minX) * disX + 50, canvas.height - (systems[systemID]['z'] - minZ) * disZ - 48.5);
				ctx.closePath();
			}
		</script>
	</body>
</html>