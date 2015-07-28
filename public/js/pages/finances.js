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

		var tmpl = $("#journalTemplate").innerHTML;
		var el = $("#taxesList");
		el.innerHTML = "";

		for(var i = 0; i < r.entries.length; i++)
			el.appendChild(createElement(tmpl.format([r.entries[i].ownerName, r.entries[i].valuestr])));

    	el.appendChild(createElement(tmpl.format(["<b>Global</b>", "<b>" + r.globalstr + "</b>"])));

		cb();
	}, "json");
}
