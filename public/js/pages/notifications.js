notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		var list = $("#notificationsList");
		var tmpl = $("#notificationTemplate").innerHTML;

		for(var i = 0; i < r.length; i++)
			list.appendChild(createElement(tmpl.format(["", r[i].subject == "!!Unable to read notification" ? JSON.stringify(r[i]) : '<h4 class="mtn mbn">' + r[i].subject + '</h4><p>' + r[i].message + '</p>'])));

		fadeOn($("#notificationsConti"), 1);
	}, "json");
}
