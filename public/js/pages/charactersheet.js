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
		for (var i = r.length - 1; i >= 0; i--)
			el.appendChild(createElement('<div class="hover row">' +
				'<img src="https://image.eveonline.com/Alliance/' + r[i].allianceID + '_32.png" alt="' + r[i].allianceName + '"/>' +
				'<img src="https://image.eveonline.com/Corporation/' + r[i].corporationID + '_32.png" alt="' + r[i].corporationName + '"/>' +
				'<img src="https://image.eveonline.com/Character/' + r[i].characterID + '_32.jpg" alt="' + r[i].characterName + '"/>' +
				'<span onclick="switchCharacter(' + r[i].characterID + ');">' + r[i].characterName + '</span>' +
				(r[i].characterID != coreStatus.charid ? '<span class="fr hover" onclick="deleteCharacter(' + r[i].characterID + ');">&times;</span>' : '') +
			'</div>'));
	}, "json");
	ajax("/json/character/" + coreStatus.charid + "/groups/", function (r) {
		var el = $("#groupList");
		el.innerHTML = '';
		for (var i = r.length - 1; i >= 0; i--)
			el.appendChild(createElement('<div> + ' + r[i].name + '</div>'));
	}, "json");
	loadOptions();
}

function loadOptions () {
	ajax("/json/character/" + coreStatus.charid + "/options/", function (s) {
		console.log(s);
		for(var key in s) {
			var list = $("#" + key + "list");
			if(list) {
				list.innerHTML = "";
				var tmpl = $("#" + key + "Template").innerHTML;
				for(var i = 0; i < s[key].length; i++) {
					switch (key) {
						case 'jid':
							var str = '<div class="hover row"><span>' + s[key][i] + '</span>';
							if(i === 0) {
								str += '<div><input type="password" name="jpw" id="jpw" class="mtn" placeholder="Jabber Password"></div><span class="btn" onclick="savePassword();">Save Password</span>';
							} else {
								str += '<span class="fr hover" onclick="delOption(\'' + key + '\',\'' + s[key][i] + '\');">×</span>';
							}
							str += '</div>';
							list.appendChild(createElement(tmpl.format([str])));
							if(i == s[key].length - 1)
								list.appendChild(createElement('<div class="hover"></div>'));
							break;
						case 'xjid':
							list.appendChild(createElement(tmpl.format([s[key][i]])));
							break;
						case 'ts3':
							list.appendChild(createElement(tmpl.format([s[key][i]])));
							break;
						case 'xts3':
							list.appendChild(createElement(tmpl.format([s[key][i]])));
							break;
					}
				}
			}
		}
	}, "json");
}

function deleteCharacter (charid) {
	ajax("/json/character/delete/" + charid + "/", function (r) {
		if(r.state == "success")
			hashChange();
	}, "json");
}

function setOption (key, value) {
	ajax("/json/character/" + coreStatus.charid + "/option/" + key + "/set/" + value + "/", function (r) {
		console.log(r);
		if(r.state == "success")
			loadOptions();
	}, "json");
	loadOptions();
}

function addOption (key, value) {
	ajax("/json/character/" + coreStatus.charid + "/option/" + key + "/add/" + value + "/", function (r) {
		console.log(r);
		if(r.state == "success")
			loadOptions();
	}, "json");
}

function delOption (key, value) {
	ajax("/json/character/" + coreStatus.charid + "/option/" + key + "/del/" + value + "/", function (r) {
		console.log(r);
		if(r.state == "success")
			loadOptions();
	}, "json");
}

function savePassword () {
	setOption("jpw", $("#jpw").value);
	$("#jpw").value = "";
}

function addJID () {
	addOption("xjid", $("#jid").value);
	$("#jid").value = "";
}

function addTS3 () {
	addOption("xts3", $("#ts3").value);
	$("#ts3").value = "";
}
