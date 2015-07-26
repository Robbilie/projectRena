notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		var list = $("#notificationsList");
		var tmpl = $("#notificationTemplate").innerHTML;

		for(var i = 0; i < r.length; i++) {
			var bod = "";
			bod += '<input type="checkbox" id="notification' + r[i].id + '" class="details"/>';
			bod += '<label for="notification' + r[i].id + '">';
			if(r[i].subject == "!!Unable to read notification") {
				bod += '<h4 class="mtn mbn">ID: ' + r[i].id + ' , TYPE:' + r[i].typeID + '</h4>';
				bod += '<p>' + JSON.stringify(r[i]) + '</p>';
			} else {
				bod += '<h4 class="mtn mbn">' + r[i].subject + '</h4>';
				bod += '<p>' + r[i].message + '</p>';
			}
			bod += '</label>';
			list.appendChild(createElement(tmpl.format([bod])));
		}

		fadeOn($("#notificationsConti"), 1);
	}, "json");
}
