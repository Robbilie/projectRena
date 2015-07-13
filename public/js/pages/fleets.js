fleetsJS();
function fleetsJS () {
  resetFleetForm();
    ajax("/json/fleets/", function (r) {
        console.log(r);

        var tmpl = $("#fleetTemplate").innerHTML;
        var el = $("#fleetslist");
        el.innerHTML = "";

        for(var i = 0; i < r.fleets.length; i++)
            el.innerHTML += tmpl.format([r.fleets[i].id, r.fleets[i].name, ""]);

        if(r.cancreate.length > 0)
          $("#fleetScope").innerHTML = '<option>' + r.cancreate.join('</option><option>') + '</option>';

        if(r.cancreate.length === 0)
            $("#createFleetForm").parentNode.removeChild($("#createFleetForm"));

        fadeOn($("#fleetsConti"), 1);
    }, "json");
}

function createFleet () {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200) {
            var r = JSON.parse(req.responseText);
            $("#createresponse").innerHTML = r.msg;
            if(r.state == "success")
                fleetsJS();
        }
    };
    req.open("POST", "/json/fleet/create/", true);
    req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    req.send(encodeURI("scope=" + $("#fleetScope").value + "&name=" + $("#fleetName").value + "&comment=" + $("#fleetComment").value + "&expiresin=" + $("#fleetExpires").value + "&participants=" + $("#fleetParticipants").value.split("\n").join(",")));
}

function resetFleetForm () {
  $("#fleetScope").value = "";
  $("#fleetName").value = "";
  $("#fleetComment").value = "";
  $("#fleetExpires").value = "";
  $("#fleetParticipants").value = "";
}
