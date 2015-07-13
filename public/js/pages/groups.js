groupsJS();
function groupsJS () {
	resetGroupFrom();
	ajax("/json/groups/", function (r) {
		console.log(r);

    if(r.cancreate.length > 0)
      $("#groupScope").innerHTML = '<option>' + r.cancreate.join('</option><option>') + '</option>';

    if(r.cancreate.length === 0)
        $("#createGroupForm").parentNode.removeChild($("#createGroupForm"));

		var tmpl = $("#groupsTemplate").innerHTML;
		var el1 = $("#groupslist");
		var el2 = $("#corporationGroupslist");
		var el3 = $("#allianceGroupslist");
		el1.innerHTML = "";
		el2.innerHTML = "";
		el3.innerHTML = "";
		for(var i = 0; i < r.owned.length; i++)
			if(r.owned[i].owner !== null || (r.owned[i].owner === null && coreStatus.isAdmin))
				el1.innerHTML += tmpl.format([r.owned[i].id,(r.owned[i].custom == 1 ? '[custom] ' : '') + r.owned[i].name, ""]);
		for(var j = 0; j < r.corporation.length; j++)
			el2.innerHTML += tmpl.format([r.corporation[j].id,(r.corporation[j].custom == 1 ? '[custom] ' : '') + r.corporation[j].name, '<span class="fr">[' + ((r.groups.indexOf(parseInt(r.corporation[j].id)) >= 0) ? "member" : (r.corporation[j].custom ? '<span onclick="apply(' + r.corporation[j].id + ');">apply</span>' : "static")) + ']</span>']);
		for(var k = 0; k < r.alliance.length; k++)
			el3.innerHTML += tmpl.format([r.alliance[k].id,(r.alliance[k].custom == 1 ? '[custom] ' : '') + r.alliance[k].name, '<span class="fr">[' + ((r.groups.indexOf(parseInt(r.alliance[k].id)) >= 0) ? "member" : (r.alliance[k].custom ? '<span onclick="apply(' + r.alliance[k].id + ');">apply</span>' : "static")) + ']</span>']);

		fadeOn($("#groupsConti"), 1);
	}, "json");
}

function createGroup () {
	ajax("/json/group/create/" + $("#groupName").value + "/" + $("#groupScope").value + "/" + $("#groupState").checked + "/", function (r) {
		if(r.state == "success") {
			groupsJS();
		} else {
			$("#createresponse").innerHTML = r.msg;
		}
	}, "json");
}

function resetGroupFrom () {
	$("#groupName").value = "";
	$("#groupScope").value = "";
	$("#groupState").checked = true;
}

function apply(groupID) {
	ajax("/json/group/" + groupID + "/apply/", function (r) {
		if(r.state == "success") {
			//groupJS();
			alert("applied");
		}
	}, "json");
}
