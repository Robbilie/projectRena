settingsJS();
function settingsJS () {
	fadeOn($("#settingsConti"), 1);
}

function submitAPI () {
	var keyID = $("[name='keyID']")[0].value;
	var vCode = $("[name='vCode']")[0].value;
	if(keyID === "" || vCode === "") return;
	ajax("/json/apikey/" + keyID + "/" + vCode + "/", function (r) {
		if(r.state == "success")
			resetAPIForm();
		$("#submitresponse").innerHTML = r.msg;
	}, "json");
}

function resetAPIForm () {
	$("#keyID").value = "";
	$("#vCode").value = "";
}
