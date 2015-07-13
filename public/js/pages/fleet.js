fleetJS();
function fleetJS () {
    ajax("/json/fleet/" + location.hash.split("/")[2] + "/", function (r) {
        console.log(r);

        $("#fleetName").innerHTML = ((r.hash === "") ? r.name : '<a href="#/fleets/confirm/' + r.hash + '/">' + r.name + '</a>') + ((r.expired) ? '<span class="fr">[expired]</span>' : '');
        $("#fleetComment").innerHTML = r.comment !== "" ? '<div class="paddedp">' + r.comment + '</div>' : '';

        var tmpl = $("#memberTemplate").innerHTML;
        var el = $("#memberList");
        el.innerHTML = "";
        for(var i = 0; i < r.participants.length; i++)
            if(r.participants[i])
                el.appendChild(createElement(tmpl.format([r.participants[i].characterID, r.participants[i].characterName, (r.participants[i].confirmed ? '<span class="fr">[confirmed]</span>' : '')])));

        fadeOn($("#fleetConti"), 1);
    }, "json");
}
