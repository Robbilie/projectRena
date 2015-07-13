structuresJS();
function structuresJS () {
	if(location.hash.split("/")[2] === "") {
		var t = $("#controltowerNav").children[0];
		location.hash = t.getAttribute("href");
		click(t);
	} else {
		$("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
		$("#" + location.hash.split("/")[2] + "Nav").className += " selected";
		var fn = "structures" + location.hash.split("/")[2] + "JS";
		if(typeof window[fn] === "function")
			window[fn](function () {
				fadeOn($("#structuresConti"), 1);
			});
	}
}

function structurescontroltowerJS (cb) {
	var tmpl = $("#controltowerStructuresTemplate").innerHTML;
	var structCont = $("#structuresContent");
	var states = ["Unanchored", "Anchored / Offline", "Onlining", "Reinforced", "Online"];
	ajax("/json/structures/controltower/", function (r) {
		var corp = "";
		var region = "";
		var system = "";
		var chngd = false;
		for (var i = 0; i < r.length; i++) {
			if(corp != r[i].corpName) {
				corp = r[i].corpName;
				structCont.appendChild(createElement('<h2 class="mtn mbn">' + corp + '</h2>'));
			}
			if(region != r[i].regionName) {
				region = r[i].regionName;
				structCont.appendChild(createElement('<h3 class="mtn mbn">' + region + '</h3>'));
			}
			if(system != r[i].solarSystemName) {
				system = r[i].solarSystemName;
				structCont.appendChild(createElement('<h4 class="mtn mbn">' + system + '</h3>'));
			}
			var fuel = 0;
			var stront = 0;
			for(var j = 0; j < r[i].content.length; j++) {
				if(r[i].content[j].group == 1136) {
					fuel += (r[i].content[j].volume * r[i].content[j].quantity);
				} else if(r[i].content[j].typeID == 16275) {
					stront += (r[i].content[j].volume * r[i].content[j].quantity);
				}
			}
			structCont.appendChild(createElement(tmpl.format([r[i].id, r[i].name, r[i].moonName, states[r[i].state], r[i].typeName, fuel / r[i].capacity * 100, stront / r[i].secondaryCapacity * 100])));
			chngd = false;
		}
		cb();
	}, "json");
}

function structureswarpdisruptorJS (cb) {
	cb();
}

function structuressovereigntyJS (cb) {
	cb();
}

function structurespersonalJS (cb) {
	cb();
}
