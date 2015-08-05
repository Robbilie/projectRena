financesJS();
function financesJS () {
	if(location.hash.split("/")[2] === "") {
		var t = $("#financesNav").children[0];
		location.hash = t.getAttribute("href");
		click(t);
	} else {
		$("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
		$("#" + location.hash.split("/")[2] + "Nav").className += " selected";
		var fn = "finances" + location.hash.split("/")[2] + "JS";
		if(typeof window[fn] === "function")
			window[fn](function () {
				fadeOn($("#financesConti"), 1);
			});
	}
}

function financestaxesJS (cb) {
	ajax("/json/finances/taxes/" + ($("#taxesFrom").value !== "" ? ($("#taxesFrom").value + ($("#taxesTill").value !== "" ? "/" + $("#taxesTill").value : "")) + '/' : ''), function (r) {
		console.log(r);

		var jourTmpl = $("#journalTemplate").innerHTML;
		var corpTmpl = $("#taxesTemplate").innerHTML;
		var el = $("#taxesList");
		el.innerHTML = "";

		for(var i = 0; i < r.length; i++) {
			el.appendChild(createElement(corpTmpl.format([r[i].name, r[i].id])));
			var l = $("#taxesList" + r[i].id);
			for(var j = 0; j < r[i].entries.length; j++)
				l.appendChild(createElement(jourTmpl.format([r[i].entries[j].ownerName, r[i].entries[j].valuestr])));
    		l.appendChild(createElement(jourTmpl.format(["<b>Global</b>", "<b>" + r[i].globalstr + "</b>"])));
		}
		
		cb();
	}, "json");
}
