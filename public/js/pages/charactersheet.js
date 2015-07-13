setTimeout(function () { charactersheetJS(); }, 300);
function charactersheetJS () {
	$("#charSwitch").href = loginurl + escape("#!/profile/");
	ajax("/json/character/" + coreStatus.charid + "/", function (r) {
		$("#characterImg").src = "https://image.eveonline.com/Character/" + r.characterID + "_128.jpg";
		$("#characterImg").alt = r.characterName;
		$("#corporationImg").src = "https://image.eveonline.com/Corporation/" + r.corporationID + "_64.png";
		$("#corporationImg").alt = r.corporationName;
		$("#allianceImg").src = "https://image.eveonline.com/Alliance/" + r.allianceID + "_64.png";
		$("#allianceImg").alt = r.allianceName;

		$("#characterName").innerHTML = r.characterName;
		$("#corporationName").innerHTML = r.corporationName;
		$("#allianceName").innerHTML = r.allianceName;

		fadeOn($("#charactersheetConti"), 1);
	}, "json");
	ajax("/json/characters/", function (r) {
		var el = $("#characterList");
		el.innerHTML = '';
		for (var i = r.length - 1; i >= 0; i--) {
			el.innerHTML += '<div class="hover row">' +
				'<img src="https://image.eveonline.com/Alliance/' + r[i].allianceID + '_32.png" alt="' + r[i].allianceName + '"/>' +
				'<img src="https://image.eveonline.com/Corporation/' + r[i].corporationID + '_32.png" alt="' + r[i].corporationName + '"/>' +
				'<img src="https://image.eveonline.com/Character/' + r[i].characterID + '_32.jpg" alt="' + r[i].characterName + '"/>' +
				'<span onclick="switchCharacter(' + r[i].characterID + ');">' + r[i].characterName + '</span>' +
				(r[i].characterID != coreStatus.charid ? '<span class="fr hover" onclick="deleteCharacter(' + r[i].characterID + ');">&times;</span>' : '') +
			'</div>';
		}
	}, "json");
	ajax("/json/character/" + coreStatus.charid + "/groups/", function (r) {
		var el = $("#groupList");
		el.innerHTML = '';
		for (var i = r.length - 1; i >= 0; i--) {
			el.innerHTML += '<div> + ' + r[i].name + '</div>';
		}
	}, "json");
}

function deleteCharacter (charid) {
	ajax("/json/character/delete/" + charid + "/", function (r) {
		if(r.state == "success")
			hashChange();
	}, "json");
}
