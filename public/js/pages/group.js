var groupScope = "default";
var groupCustom = 0;
var groupPermissions = 0;
var groupCanEdit = false;
var groupCanAdd = false;
var namedd;
var charToAdd = null;
groupJS();
function groupJS () {
	ajax("/json/group/" + location.hash.split("/")[2] + "/", function (r) {
		console.log(r);
		$("#groupName").innerHTML = r.group.name + '<span class="fr">[' + (r.group.custom == 1 ? 'custom ' : '') + r.group.scope + ']</span>';
		groupScope = r.group.scope;
		groupCustom = r.group.custom;
		groupPermissions = r.group.permissions;
		groupCanEdit = r.canEdit;
		groupCanAdd = r.canAdd;

		if(!r.group.custom || !r.canAdd) {
			$("#charAddCard").parentNode.removeChild($("#charAddCard"));
		} else {
            namedd = new AutoComplete("charId");
            namedd.oncomplete = function (self, el) {
                charToAdd = el.getAttribute("data-dat");
            };
		}

		loadPermissions();

		loadMembers();

		if(r.canAdd) {
			loadApplications();
		} else {
			$("#applicationsCard").parentNode.removeChild($("#applicationsCard"));
		}

		fadeOn($("#groupConti"), 1);
	}, "json");
}

function loadPermissions () {
	ajax("/json/permissions/" + groupScope + "/", function (s) {
		console.log(s);
		var tmpl = $("#permissionTemplate").innerHTML;
		var el = $("#permissionList");
		el.innerHTML = "";
		for(var i = 0; i < s.length; i++)
			el.innerHTML += tmpl.format([((groupPermissions.indexOf(parseInt(s[i].id)) >= 0) ? 'checked="true"' : '') + (groupCanEdit ? '' : ' disabled'), s[i].name, s[i].id, location.hash.split("/")[2]]);
	}, "json");
}

function loadMembers () {
	ajax("/json/group/" + location.hash.split("/")[2] + "/members/", function (t) {
		var tmpl = $("#memberTemplate").innerHTML;
		var el = $("#memberList");
		el.innerHTML = "";
		for(var i = 0; i < t.length; i++)
			el.innerHTML += tmpl.format([t[i].characterID, t[i].characterName, groupCustom == 1 ? '<span class="fr hover" onclick="removeCharacter(' + t[i].characterID + ');">&times;</span>' : '']);
	}, "json");
}

function loadApplications () {
	ajax("/json/group/" + location.hash.split("/")[2] + "/applications/", function (t) {
		var tmpl = $("#applicationTemplate").innerHTML;
		var el = $("#applicationList");
		el.innerHTML = "";
		for(var i = 0; i < t.length; i++)
			el.innerHTML += tmpl.format([t[i].characterID, t[i].characterName, ""]);
	}, "json");
}

function changePermission (el) {
	ajax("/json/group/" + location.hash.split("/")[2] + "/" + (el.checked ? "add" : "remove") + "/permission/" + el.getAttribute("data-permissionid") + "/", function (r) {
		console.log(r);
		if(r.state == "error")
			el.checked = !el.checked;
	}, "json");
}

function removeCharacter (charid) {
	ajax("/json/group/" + location.hash.split("/")[2] + "/remove/character/" + charid + "/", function (r) {
		if(r.state == "success")
			loadMembers();
	}, "json");
}

function addCharacter () {
	ajax("/json/group/" + location.hash.split("/")[2] + "/add/character/" + charToAdd + "/", function (r) {
		if(r.state == "success") {
			loadMembers();
		} else {
			$("#addresponse").innerHTML = "error";
		}
	}, "json");
}

function acceptApplication (characterID) {
	ajax("/json/group/" + location.hash.split("/")[2] + "/application/" + characterID + "/accept/", function (r) {
		if(r.state == "success") {
			loadApplications();
			loadMembers();
		}
	}, "json");
}

function rejectApplication (characterID) {
	ajax("/json/group/" + location.hash.split("/")[2] + "/application/" + characterID + "/reject/", function (r) {
		if(r.state == "success")
			loadApplications();
	}, "json");
}
