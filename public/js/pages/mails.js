mailsJS();
function mailsJS () {
	if(location.hash.split("/")[2] === "") {
		var t = $("#allNav").children[0];
		location.hash = t.getAttribute("href");
		click(t);
	} else {
		$("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
		$("#" + location.hash.split("/")[2] + "Nav").className += " selected";
		var fn = "mails" + location.hash.split("/")[2] + "JS";
		if(typeof window[fn] === "function")
			window[fn](function () {
				fadeOn($("#mailsConti"), 1);
			});
	}
}

function mailsallJS (cb) {
	ajax("/json/mails/all/", function (r) {
		var tmpl = $("#mailsTemplate").innerHTML;
		var mailsCont = $("#mailsContent");

		for(var i = 0; i < r.length; i++)
			mailsCont.appendChild(createElement(tmpl.format(r[i])));

		cb();
	}, "json");
}

function mailspersonalJS (cb) {
	ajax("/json/mails/personal/", function (r) {
		var tmpl = $("#mailsTemplate").innerHTML;
		var mailsCont = $("#mailsContent");

		for(var i = 0; i < r.length; i++)
			mailsCont.appendChild(createElement(tmpl.format(r[i])));

		cb();
	}, "json");
}

function mailscorporationJS (cb) {
	ajax("/json/mails/corporation/", function (r) {
		var tmpl = $("#mailsTemplate").innerHTML;
		var mailsCont = $("#mailsContent");

		for(var i = 0; i < r.length; i++)
			mailsCont.appendChild(createElement(tmpl.format(r[i])));

		cb();
	}, "json");
}

function mailsallianceJS (cb) {
	ajax("/json/mails/alliance/", function (r) {
		var tmpl = $("#mailsTemplate").innerHTML;
		var mailsCont = $("#mailsContent");

		for(var i = 0; i < r.length; i++)
			mailsCont.appendChild(createElement(tmpl.format(r[i])));

		cb();
	}, "json");
}
