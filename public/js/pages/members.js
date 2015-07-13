membersJS();
function membersJS () {
	if(location.hash.split("/")[2] === "") {
		var t = $("#coporationNav").children[0];
		location.hash = t.getAttribute("href");
		click(t);
	} else {
		$("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
		$("#" + location.hash.split("/")[2] + "Nav").className += " selected";
		var fn = "members" + location.hash.split("/")[2] + "JS";
		if(typeof window[fn] === "function")
			window[fn](function () {
				fadeOn($("#membersConti"), 1);
			});
	}
}

function memberscorporationJS (cb) {
	if(location.hash.split("/")[3] === "") {
		ajax("/json/character/" + coreStatus.charid + "/", function (r) {
			listCorpMembers(r.corporationID, cb);
		}, "json");
	} else {
		listCorpMembers(location.hash.split("/")[3], cb);
	}
}

function listCorpMembers (id, cb) {
	ajax("/json/corporation/" + id + "/", function (r) {
		$("#memberTitle").innerHTML = r.name;
		ajax("/json/corporation/" + id + "/members/", function (s) {
			var tmpl = $("#memberTemplate").innerHTML;
			var el = $("#corporationList");
			el.innerHTML = "";
			for(var i = 0; i < s.length; i++)
				el.appendChild(createElement(tmpl.format([s[i].characterID, s[i].characterName, ''])));
			cb();
		}, "json");
	}, "json");

}

function membersallianceJS (cb) {
	if(location.hash.split("/")[3] === "") {
		ajax("/json/character/" + coreStatus.charid + "/", function (r) {
			listAlliMembers(r.allianceID, cb);
		}, "json");
	} else {
		listAlliCorps(location.hash.split("/")[3], cb);
	}
}

function listAlliMembers (id, cb) {
	ajax("/json/alliance/" + id + "/", function (r) {
		$("#memberTitle").innerHTML = r.name;
		ajax("/json/alliance/" + id + "/members/", function (s) {
			var tmpl = $("#memberTemplate").innerHTML;
			var el = $("#allianceList");
			el.innerHTML = "";
			for(var i = 0; i < s.length; i++)
				el.appendChild(createElement(tmpl.format([s[i].characterID, s[i].characterName, ''])));
			cb();
		}, "json");
	}, "json");
}
