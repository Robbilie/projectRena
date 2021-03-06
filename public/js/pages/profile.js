profileJS();
function profileJS () {
	ajax("/json/character/" + location.hash.split("/")[2] + "/", function (r) {
		$("#characterImg").src = "https://imageserver.eveonline.com/Character/" + r.characterID + "_128.jpg";
		$("#characterImg").alt = r.characterName;
		$("#corporationImg").src = "https://imageserver.eveonline.com/Corporation/" + r.corporationID + "_64.png";
		$("#corporationImg").alt = r.corporationName;
		$("#allianceImg").src = "https://imageserver.eveonline.com/Alliance/" + r.allianceID + "_64.png";
		$("#allianceImg").alt = r.allianceName;

		$("#characterName").innerHTML = r.characterName;
		$("#corporationName").innerHTML = r.corporationName;
		$("#allianceName").innerHTML = r.allianceName;

		fadeOn($("#profileConti"), 1);
	}, "json");
	ajax("/json/character/" + location.hash.split("/")[2] + "/groups/", function (r) {
		var el = $("#groupList");
		el.innerHTML = '';
		for (var i = r.length - 1; i >= 0; i--)
			el.appendChild(createElement('<div> + ' + r[i].name + '</div>'));
		if(r.length == 0)
			fadeOn(el.parentNode, 0);
		else
			fadeOn(el.parentNode, 1);
	}, "json");
}
