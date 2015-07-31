contentsJS();
function contentsJS () {
	ajax("/json" + location.hash.slice(2), function (r) {
		console.log(r);
		$("#contentsname").innerHTML = r.name;
		var tmpl = $("#contentsTemplate").innerHTML;
		var el = $("#contentlist");
		el.innerHTML = "";
		for(var i = 0; i < r.list.length; i++)
			el.appendChild(createElement(tmpl.format([r.list[i].flag === 0 ? 'href="#!/corporation/' + r.list[i].ownerID + '/location/' + r.list[i].itemID + '/"' : '', r.list[i].name ? r.list[i].name : "", r.list[i].typeName + " " + r.list[i].quantity.format(0,3,".") + "x"])));

		fadeOn($("#contentsConti"), 1);
	}, "json");
}
