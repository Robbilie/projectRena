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
			el.appendChild(createElement(tmpl.format([r.modules[i].ownerID, r.modules[i].itemID, r.modules[i].name, [404,416,438].indexOf(r.modules[i].group) != -1 ? 'dragable="true" data-modid="' + r.modules[i].itemID + '"' : ''])));

		fadeOn($("#controltowerConti"), 1);
	}, "json");
}

function dropReaction (e, el) {
    e.preventDefault();
    var data = e.dataTransfer.getData("text");
    console.log(data);
	removeAddReaction(el.className == "card" ? el.children[1] : el);
}

function dragOverReaction (e, el) {
	e.preventDefault();
	if(el.className == "card") {
		if(el.children[1].lastChild.className != "btn")
			el.children[1].appendChild(createElement('<div class="btn">+</div>'));
	} else {
		if(el.lastChild.className != "btn")
			el.appendChild(createElement('<div class="btn">+</div>'));
	}
}

function dragLeaveReaction (e, el) {
	removeAddReaction(el.className == "card" ? el.children[1] : el);
}

function removeAddReaction (el) {
	for(var i = el.children.length - 1; i >= 0; i--) {
		if(el.children[i].className == "btn") {
			el.removeChild(el.children[i]);
		} else {
			break;
		}
	}
}
