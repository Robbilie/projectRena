notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		var notList = $("#notificationsList");
		var tasList = $("#tasksList");
		var tmpl = $("#notificationTemplate").innerHTML;

		for(var i = 0; i < r.length; i++) {
			var bod = "";
			if(r[i].requested == r[i].created)
				bod += '<span class="fr">' + (new Date(r[i].created * 1000).toLocaleString()) + '</span>';
			bod += '<input type="checkbox" id="notificationToggle' + r[i].id + '" onchange="markRead(' + r[i].id + ');" class="details"/>';
			bod += '<label for="notificationToggle' + r[i].id + '">';
			if(r[i].subject == "!!Unable to read notification") {
				bod += '<h4 class="mtn mbn">ID: ' + r[i].id + ' , TYPE: ' + r[i].typeID + '</h4>';
				bod += '<p>' + JSON.stringify(r[i]) + '</p>';
			} else {
				bod += '<h4 class="mtn mbn">' + r[i].subject + '</h4>';
				bod += '<p>' + r[i].message + '</p>';
			}
			bod += '</label>';
			if(r[i].requested > r[i].created) {
				tasList.insertBefore(createElement(tmpl.format([r[i].id, r[i].typeID, bod, r[i].readState ? '' : 'unread'])), tasList.firstChild);
			} else {
				notList.appendChild(createElement(tmpl.format([r[i].id, r[i].typeID, bod, r[i].readState ? '' : 'unread'])));
			}
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

function markRead (notificationID) {
	ajax("/json/notification/" + notificationID + "/read/", function (r) {
		if(r.state == "success") {
			$('#notification' + notificationID).className = $('#notification' + notificationID).className.replace("unread", "");
			console.log(notificationID + " marked as read");
		}
	}, "json");
}