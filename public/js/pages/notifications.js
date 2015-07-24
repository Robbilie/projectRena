notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		var list = $("#notificationsList");
		var tmpl = $("#notificationTemplate").innerHTML;

		for(var i = 0; i < r.length; i++)
			list.appendChild(createElement(tmpl.format(["", JSON.stringify(r[i])])));

		fadeOn($("#notificationsConti"), 1);
	}, "json");
}
