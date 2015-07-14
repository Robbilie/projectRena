structurescontroltowerJS();
function structurescontroltowerJS () {
	ajax("/json" + location.hash.slice(2), function (r) {
		console.log(r);
		var states = ["Unanchored", "Anchored / Offline", "Onlining", "Reinforced", "Online"];

		$("#controltowernotif").onmouseover = function () { loadNotif(r.id, this); };

		$("#controltowername").innerHTML = r.name;
		$("#controltowermoonname").innerHTML = r.moonName;
		$("#controltowerstatename").innerHTML = states[r.state];
		$("#controltowertypename").innerHTML = r.typeName;
		$("#controltowerfuel").style.width = r.fuel + "%";
		$("#controltowerstrontium").style.width = r.strontium + "%";

		var tmpl = $("#modulesTemplate").innerHTML;
		var el = $("#moduleslist");
		el.innerHTML = "";
		for(var i = 0; i < r.modules.length; i++) {
			var ne = createElement(tmpl.format([r.modules[i].ownerID, r.modules[i].itemID, r.modules[i].name]));
			if([404,416,438].indexOf(r.modules[i].group) != -1) {
				ne.setAttribute("draggable", "true");
				ne.setAttribute("data-modid", r.modules[i].itemID);
				ne.setAttribute("ondragstart", "dragReaction(event, this)");
			}
			el.appendChild(ne);
		}

		for(var j = 0; j < r.reactions.length; j++) {
			if(!$("#react" + r.reactions[j].destination)) {
				if($('[data-modid="' + r.reactions[j].destination + '"]')[0]) {
					createNewReaction($('[data-modid="' + r.reactions[j].destination + '"]')[0].cloneNode(true));
				} else {
					break;
				}
			}
			if(!$("#react" + r.reactions[j].source)) {
				if($('[data-modid="' + r.reactions[j].source + '"]')[0]) {
					createNewReaction($('[data-modid="' + r.reactions[j].source + '"]')[0].cloneNode(true));
				} else {
					break;
				}
			}

			$("#react" + r.reactions[j].destination).children[1].appendChild($("#react" + r.reactions[j].source));
			$("#react" + r.reactions[j].destination).children[1].className = $("#react" + r.reactions[j].destination).children[1].className.replace(/( split[0-5])/g, "");
			$("#react" + r.reactions[j].destination).children[1].className += " split" + $("#react" + r.reactions[j].destination).children[1].children.length;
		}

		$("#reactions").children[1].className = $("#reactions").children[1].className.replace(/( split[0-5])/g, "");
		$("#reactions").children[1].className += " split" + $("#reactions").children[1].children.length;

		fadeOn($("#controltowerConti"), 1);
	}, "json");
}

function dragReaction (e, el) {
	e.stopPropagation();
	e.dataTransfer.setData('draggedEl', el.outerHTML);
}

function dropReaction (e, el) {
	e.stopPropagation();
    e.preventDefault();
	removeAddReaction(el);

    var data = e.dataTransfer.getData("draggedEl");
    var oe = createElement(data);
    var ne;
    if(oe.id) {
    	ne = oe;
		var par = $("#" + oe.id).parentNode;
    	par.removeChild($("#" + oe.id));
		par.className = par.className.replace(/( split[0-5])/g, "");
		par.className += " split" + par.children.length;
    } else {
	    oe.removeAttribute("ondragstart");
	    oe.removeAttribute("draggable");
		ne = createElement('<div class="react"><div class="rhead"></div><div class="rbody split0"></div>');
		ne.firstChild.appendChild(oe);
		ne.setAttribute("ondrop", "dropReaction(event, this)");
		ne.setAttribute("ondragover", "dragOverReaction(event, this)");
		ne.setAttribute("ondragleave", "dragLeaveReaction(event, this)");
		ne.setAttribute("ondragstart", "dragReaction(event, this)");
		ne.setAttribute("draggable", "true");
		ne.setAttribute("data-modid", oe.getAttribute("data-modid"));
		ne.id = "react" + oe.getAttribute("data-modid");
    }

	el.children[1].appendChild(ne);
	el.children[1].className = el.children[1].className.replace(/( split[0-5])/g, "");
	el.children[1].className += " split" + el.children[1].children.length;

	if(el.className != "card")
		ajax("/json" + location.hash.slice(2) + "reaction/" + ne.getAttribute("data-modid") + "/" + el.getAttribute("data-modid") + "/", function (r) {
			console.log(r);
		}, "json");
}

function createNewReaction (oe) {
	oe.removeAttribute("ondragstart");
	oe.removeAttribute("draggable");
	var ne = createElement('<div class="react"><div class="rhead"></div><div class="rbody split0"></div>');
	ne.firstChild.appendChild(oe);
	ne.setAttribute("ondrop", "dropReaction(event, this)");
	ne.setAttribute("ondragover", "dragOverReaction(event, this)");
	ne.setAttribute("ondragleave", "dragLeaveReaction(event, this)");
	ne.setAttribute("ondragstart", "dragReaction(event, this)");
	ne.setAttribute("draggable", "true");
	ne.setAttribute("data-modid", oe.getAttribute("data-modid"));
	ne.id = "react" + oe.getAttribute("data-modid");

	$("#reactions").children[1].appendChild(ne);
	$("#reactions").children[1].className = $("#reactions").children[1].className.replace(/( split[0-5])/g, "");
	$("#reactions").children[1].className += " split" + $("#reactions").children[1].children.length;
}

function dragOverReaction (e, el) {
	e.stopPropagation();
	e.preventDefault();
	if(!el.children[2] || el.children[2].className != "btn absfill")
		el.appendChild(createElement('<div class="btn absfill">+</div>'));
}

function dragLeaveReaction (e, el) {
	removeAddReaction(el);
}

function removeAddReaction (el) {
	for(var i = el.children.length - 1; i >= 0; i--) {
		if(el.children[i].className == "btn absfill") {
			el.removeChild(el.children[i]);
		} else {
			break;
		}
	}
}
