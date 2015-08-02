intelJS();
function intelJS () {
    if(location.hash.split("/")[2] === "") {
        var t = $("#systemNav").children[0];
        location.hash = t.getAttribute("href");
        click(t);
    } else {
        $("#" + location.hash.split("/")[2] + "Nav").className.replace(" selected", "");
        $("#" + location.hash.split("/")[2] + "Nav").className += " selected";
        var fn = "intel" + location.hash.split("/")[2] + "JS";
        if(typeof window[fn] === "function")
            window[fn](function () {
                fadeOn($("#intelConti"), 1);
            });
    }
}

var systemAjax;
var regionAjax;

var checkInt;
var dd;
var intelSystemID;
function intelsystemJS (cb) {
    if(regionAjax)
        regionAjax.abort();

    $("#intelSystem").className = "";
    if(location.hash.split("/")[3] !== "")
        $("#checkTracker").checked = false;

    dd = new AutoComplete($("#intelSystemName"));
    dd.oncomplete = function (self, el) {
        intelSystemID = el.getAttribute("data-dat");
        if(intelSystemID != location.hash.split("/")[3]) {
            location.hash = "#!/intel/system/" + intelSystemID + "/";
            click();
        }
    };

    checkSystemStatus();
    cb();
}

function intelregionJS (cb) {
    if(systemAjax)
        systemAjax.abort();

    ajax("/map/region/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] : 10000029) + "/", function (r) {
        $("#regionFrame").outerHTML = r;
        var svg = $("#intelRegion").children[0];
        svg.style.background = "transparent";

        var systems = svg.getElementsByClassName("sys");
        for(var s in systems) {
            if(typeof(systems[s]) == "object") {
                // set white bg
                var nod = systems[s].getElementsByClassName("s")[0];
                if(nod && nod.style) nod.style.fill = "white";

                // set links
                var nam = systems[s].getAttribute("xlink:href").replace("http://evemaps.dotlan.net/system/","").replace("http://evemaps.dotlan.net/map/","");
                nam = nam.split("/");
                nam = nam[nam.length - 1];
                nam = nam.replace("_", " ");

                systems[s].setAttribute("xlink:href", "/#!/intel/system/" + systems[s].parentNode.id.replace("def", "") + "/");
            }
        }
        refreshDom();
        $("#intelRegion").className = "";

        checkRegionStatus();

    });

    cb();
}

var switchedIntel = false;

var systemStatus = {};
function checkSystemStatus () {
    if(!(location.hash.split("/")[1] == "intel" && location.hash.split("/")[2] == "system") || switchedIntel) {
        return;
    }
    systemAjax = ajax("/json/intel/system/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] + "/" : "") + "?hash=" + md5(JSON.stringify(systemStatus)), function (r) {
        if(JSON.stringify(systemStatus) != JSON.stringify(r)) {
            systemStatus = r;

            $("#intelConti").className = "contentConti intelStatus" + systemStatus.state;

            $("#intelStatus").innerHTML = "[" + systemStatus.status + "]";
            $("#intelLocation").innerHTML = systemStatus.regionName + " - " + systemStatus.systemName;

            $("#regionNav").children[0].href = "#!/intel/region/" + systemStatus.regionID + "/";

            if($("#intelSystemName").value === "") {
                $("#intelSystemName").value = systemStatus.systemName;
                intelSystemID = systemStatus.systemID;
            }

            var wtpl = $("#warningTemplate").innerHTML;
            var warns = $("#intelWarnings");
            warns.innerHTML = "";
            for(var i = 0; i < systemStatus.neighbours.length; i++)
                if(systemStatus.neighbours[i].hostilecount > 0)
                    warns.appendChild(createElement(wtpl.format([systemStatus.neighbours[i].systemID, systemStatus.neighbours[i].systemName, systemStatus.neighbours[i].hostilecount])));

            //var tmpl = $("#" + (systemStatus.membertype == "characters" ? "char" : "alli") + "Template").innerHTML;
            var el = $("#intelMemberlist");
            el.innerHTML = "";
            for(var j = 0; j < systemStatus.members.length; j++)
                el.appendChild(
                    createElement(
                        $("#" + systemStatus.members[j].type + "Template").innerHTML.format(
                            [systemStatus.members[j].id, 
                            systemStatus.members[j].name + (systemStatus.members[j].count ? " [" + systemStatus.members[j].count + "]" : ""), 
                            (systemStatus.members[j].standing <= 0 ? "negative" : "positive") + "Standing", 
                            (new Date(systemStatus.members[j].timestamp * 1000).toLocaleString()), 
                            systemStatus.members[j].info ? systemStatus.members[j].info : "", 
                            systemStatus.members[j].standing > 0 ? "hide" : ""
                            ]
                        )
                    )
                );
        }

        setTimeout(checkSystemStatus, 100);
    }, "json");
}

var regionStatus = {};
function checkRegionStatus () {
    if(!(location.hash.split("/")[1] == "intel" && location.hash.split("/")[2] == "region") || switchedIntel) {
        return;
    }
    regionAjax = ajax("/json/intel/region/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] + "/" : "") + "?hash=" + md5(JSON.stringify(regionStatus)), function (r) {
        if(JSON.stringify(regionStatus) != JSON.stringify(r)) {
            regionStatus = r;

            var svg = $("#svgdoc");
            for(var i = 0; i < regionStatus.length; i++) {
                var el = svg.getElementById("def" + regionStatus[i].systemID);
                var s = el.getElementsByClassName("s")[0];
                s.style.fill = regionStatus[i].hostilecount > 0 ? "orange" : "white";
            }

            svg.style.visibility = "hidden";
            setTimeout(function () { svg.style.visibility = "visible"; }, 0);

        }

        setTimeout(checkRegionStatus, 100);
    }, "json");
}

var submitting = false;
function paste (e) {
    console.log("Pasted");
    if (e.preventDefault) {
        e.stopPropagation();
        e.preventDefault();
    }
    if(submitting) return false;
    submitting = true;
    var dat = e.clipboardData.getData('text/plain');
    dat = dat.split("\r").join("").split("\n");
    submitIntel(dat);
}

function submitIntel (dat) {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200) {
            alert("Submitted");
            submitting = false;
        }
    };
    req.open("POST", "/json/intel/system/" + (!intelSystemID ? "" : intelSystemID + "/"), true);
    req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    req.send(encodeURI("local=" + dat.join(",")));
}

function toggleTrack (el) {
    if(el.checked) {
        location.hash = "#!/intel/system/";
            click();
    }
}

function setInfo (characterID) {
    var info = $("#intelInfo" + characterID).value;
    ajax("/json/intel/character/" + characterID + "/info/" + escape(info) + "/", function (r) {
        if(r.state == "success")
            console.log("successfully updated info");
    }, "json");
}