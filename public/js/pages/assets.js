assetsJS();
function assetsJS () {
	if(location.hash.split("/")[2] === "") {
		var t = $("#personalNav").children[0];
		location.hash = t.getAttribute("href");
		click(t);
	} else {
		$("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
		$("#" + location.hash.split("/")[2] + "Nav").className += " selected";
		var fn = "assets" + location.hash.split("/")[2] + "JS";
		if(typeof window[fn] === "function")
			window[fn](function () {
				fadeOn($("#assetsConti"), 1);
			});
	}
}

function assetspersonalJS (cb) {
	cb();
}

function assetscorporationJS (cb) {
	cb();
}

function assetsallianceJS (cb) {
	cb();
}

function assetsmemberJS (cb) {
	cb();
}
