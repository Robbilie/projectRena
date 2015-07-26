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
				bod += '<h4 class="mtn mbn">ID: ' + r[i].id + ' , TYPE: ' + r[i].typeID + '</h4>';
				bod += '<p>' + JSON.stringify(r[i]) + '</p>';
			} else {
				bod += '<h4 class="mtn mbn">' + r[i].subject + '</h4>';
				bod += '<p>' + r[i].message + '</p>';
			}
			bod += '</label>';
			list.appendChild(createElement(tmpl.format([r[i].typeID, bod])));
		}

		fadeOn($("#notificationsConti"), 1);
	}, "json");
	ajax("/json/notifications/types/", function (r) {
		var conti = $("#notificationsConti");
		var settings = $("#notificationForm");
		var css = "";
		for(var i = 0; i < r.length; i++) {
			var tid = 'notificationType' + r[i].typeID;
			conti.parentNode.insertBefore(createElement('<input type="checkbox" class="details" id="' + tid + '" checked/>'), conti);
			settings.appendChild(createElement('<div><label for="' + tid + '">' + r[i].name + '</label></div>'));
			css += '#' + tid + ':not(:checked) ~ #notificationSettings label[for="' + tid + '"] { color: gray; } ';
			css += '#' + tid + ':not(:checked) ~ #notificationsConti div[data-type="' + r[i].typeID + '"] { display: none; } ';
		}
		conti.parentNode.appendChild(createElement('<style type="text/css">' + css + '</style>'));
	}, "json");
}
