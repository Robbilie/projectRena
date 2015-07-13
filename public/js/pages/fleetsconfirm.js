fleetsconfirmJS();
function fleetsconfirmJS () {
    ajax("/json/fleets/confirm/" + location.hash.split("/")[3] + "/", function (r) {
        console.log(r);
    }, "json");
}
