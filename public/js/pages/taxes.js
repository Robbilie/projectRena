taxesJS();
function taxesJS () {
	ajax("/json/corporation/0/wallet/" + ($("#taxesFrom").value !== "" ? ($("#taxesFrom").value + ($("#taxesTill").value !== "" ? "/" + $("#taxesTill").value : "")) + '/' : ''), function (r) {
		console.log(r);

		var tmpl = $("#journalTemplate").innerHTML;
		var el = $("#taxesList");
		el.innerHTML = "";

		for(var i = 0; i < r.entries.length; i++)
			el.appendChild(createElement(tmpl.format([r.entries[i].ownerName, r.entries[i].valuestr])));

    el.appendChild(createElement(tmpl.format(["<b>Global</b>", "<b>" + r.globalstr + "</b>"])));

		fadeOn($("#taxesConti"), 1);
	}, "json");
}
