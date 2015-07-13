notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		fadeOn($("#notificationsConti"), 1);
	}, "json");
}
