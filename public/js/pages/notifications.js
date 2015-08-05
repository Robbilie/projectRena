notificationsJS();
function notificationsJS () {
	ajax("/json/notifications/", function (r) {
		console.log(r);
		var notList = $("#notificationsList");
		var tasList = $("#tasksList");
		var tmpl = $("#notificationTemplate").innerHTML;

		for(var i = 0; i < r.length; i++) {
			var tmpel = createElement(tmpl.format(
				[
					r[i].id, 
					r[i].typeID, 
					r[i].requested == r[i].created ? '<span class="fr">' + new Date(r[i].created * 1000).toJSON().split(".")[0] + '</span>' : "", 
					r[i].readState ? '' : 'unread', 
					r[i].subject == "!!Unable to read notification" ? 'ID: ' + r[i].id + ' , TYPE: ' + r[i].typeID : r[i].subject, 
					r[i].subject == "!!Unable to read notification" ? JSON.stringify(r[i]) : r[i].message
				]
			));
			if(r[i].requested > r[i].created) {
				if(r[i].state == 0)
					tasList.insertBefore(tmpel, tasList.firstChild);
			} else {
				notList.appendChild(tmpel);
			}
		}

		fadeOn($("#notificationsConti"), 1);
	}, "json");
	ajax("/json/notifications/types/", function (r) {
		var conti = $("#notificationsConti");
		var settings = $("#notificationForm");
		settings.appendChild(createElement('<div><h4 class="click mtn mbn" onclick="markAllRead();">Mark All as Read</h4></div>'));
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
			setUnreadCnt();
		}
	}, "json");
}

function markAllRead () {
	ajax("/json/notifications/read/", function (r) {
		if(r.state == "success")
			hashChange();
	}, "json");
}