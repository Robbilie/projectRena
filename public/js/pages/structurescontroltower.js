structurescontroltowerJS();
function structurescontroltowerJS () {
	ajax("/json" + location.hash.slice(2), function (r) {
		var states = ["Unanchored", "Anchored / Offline", "Onlining", "Reinforced", "Online"];

		$("#controltowernotif").onmouseover = function () { loadNotif(r.id, this); };

		$("#controltowername").innerHTML = r.name;
		$("#controltowermoonname").innerHTML = r.moonname;
		$("#controltowerstatename").innerHTML = states[r.state];
		$("#controltowertypename").innerHTML = r.typename;
		$("#controltowerfuel").style.width = r.fuel + "%";
		$("#controltowerstrontium").style.width = r.strontium + "%";

		var tmpl = $("#modulesTemplate").innerHTML;
		var el = $("#moduleslist");
		el.innerHTML = "";
		for(var i = 0; i < r.modules.length; i++)
			el.innerHTML += tmpl.format([r.modules[i].ownerID, r.modules[i].itemID, r.modules[i].name]);

		fadeOn($("#controltowerConti"), 1);
	}, "json");
}
