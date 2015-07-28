timersJS();
function timersJS () {
	resetTimerForm();
	ajax("/json/timers/", function (r) {
		console.log(r);

		var tmpl = $("#timerTemplate").innerHTML;
		var el = $("#timersList");
		el.innerHTML = "";

		for(var i = 0; i < r.timers.length; i++) {
			r.timers[i].timestamp = (new Date(r.timers[i].timestamp * 1000).toLocaleString());
			el.innerHTML += tmpl.format(r.timers[i]);
		}

	    var acOwner = new AutoComplete($("#timerOwner"));
	    acOwner.oncomplete = function (self, el) {
	        self.input.setAttribute("data-id", el.getAttribute("data-dat"));
	    };

	    var acType = new AutoComplete($("#timerStrucure"));
	    acType.oncomplete = function (self, el) {
	        self.input.setAttribute("data-id", el.getAttribute("data-dat"));
	    };

	    var acLoca = new AutoComplete($("#timerLocation"));
	    acLoca.oncomplete = function (self, el) {
	        self.input.setAttribute("data-id", el.getAttribute("data-dat"));
	    };

		for(var j = 0; j < r.cancreate.length; j++)
			$("#timerScope").appendChild(createElement('<option>' + r.cancreate[j] + '</option>'));

		if(r.cancreate.length === 0)
			$("#createTimerForm").parentNode.removeChild($("#createTimerForm"));


		fadeOn($("#timersConti"), 1);
	}, "json");
}

function createTimer () {
	var req = new XMLHttpRequest();
	req.onreadystatechange = function () {
		if(req.readyState == 4 && req.status == 200) {
			var r = JSON.parse(req.responseText);
			$("#createresponse").innerHTML = r.msg;
			if(r.state == "success") {
					timersJS();
			} else {
				$("#createresponse").innerHTML = r.msg;
			}
		}
	};
	req.open("POST", "/json/timer/create/", true);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	req.send(encodeURI("scope=" + $("#timerScope").value + "&ownerID=" + $("#timerOwner").getAttribute("data-id") + "&typeID=" + $("#timerStrucure").getAttribute("data-id") + "&locationID=" + $("#timerLocation").getAttribute("data-id") + "&rf=" + $("#timerRf").value + "&comment=" + $("#timerComment").value + "&timestamp=" + $("#timerTime").value));
}

function resetTimerForm () {
	$("#timerScope").value = "";
	$("#timerOwner").value = "";
	$("#timerStrucure").value = "";
	$("#timerLocation").value = "";
	$("#timerRf").value = 0;
	$("#timerTime").value = "";
	$("#timerComment").value = "";
}
