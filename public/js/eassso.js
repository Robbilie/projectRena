
window.onload = function () {
	$("#loginurl").href = loginurl + escape(location.pathname) + escape(location.search) + escape(location.hash);
    if(window != window.top)
        document.body.className = "iframe";
    if(!document.cookie.match(new RegExp("cookieandeula" + '=([^;]+)')))
        $("#checkCookieAndEula").checked = true;
    checkStatus(); //setTimeout(hashChange, 300);
};

function saveCookie () {
    var expiryDate = new Date();
    expiryDate.setFullYear(expiryDate.getFullYear() + 1);
    document.cookie = "cookieandeula" + '=y; expires=' + expiryDate.toGMTString();
}

var coreStatus = {};

function checkStatus (cb, poll) {
    ajax("/json/status/?hash=" + md5(JSON.stringify(coreStatus)), function (r) {
        $("#checkLoggedin").checked = !(!r.isLoggedin);
        setTimeout(function () {
            checkStatus(null, poll);
        }, 1000);
        if(coreStatus.charid != r.charid) {
        	setupForm();
        	fillCharlist();
        }
        coreStatus = r;
        refreshDom();
        if(cb)
            cb();
    }, "json", { retry: true });
}

function setupForm () {
	var el = $("#hiddenParams");
	el.innerHTML = "";
	var params = location.search.slice(1).split("&");
	for(var i = 0; i < params.length; i++) {
		el.appendChild(createElement('<input type="hidden" name="' + params[i].split("=")[0] + '" value="' + params[i].split("=")[1] + '"/>'));
	}
}

function fillCharlist () {
	ajax("/json/characters/", function (r) {
        var el = $("#charSelect");
        el.innerHTML = '';
        el.appendChild(createElement('<option value="' + coreStatus.charid + '">' + '<img src="https://imageserver.eveonline.com/Character/' + coreStatus.charid + '_32.jpg" alt="' + coreStatus.charname + '"/>' +
                '<span>' + coreStatus.charname + '</span>' + '</option>'));
        for (var i = r.length - 1; i >= 0; i--) {
            if(r[i].characterID == coreStatus.charid) continue;
            el.appendChild(createElement('<option value="' + r[i].characterID + '">' + '<img src="https://imageserver.eveonline.com/Character/' + r[i].characterID + '_32.jpg" alt="' + r[i].characterName + '"/>' +
                '<span>' + r[i].characterName + '</span>' + '</option>'));
        }
    }, "json");
}