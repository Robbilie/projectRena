intelJS();
function intelJS () {
    intelSystemID = null;
    systemStatus = {};
    regionStatus = {};
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

    if(window.CCPEVE)
        CCPEVE.requestTrust('https://*.eneticum.rep.pm/*');

    checkSystemStatus();
    cb();
}

function intelregionJS (cb) {
    if(systemAjax)
        systemAjax.abort();
    
    $("#intelRegion").className = "";

    var obj = document.createElement("object");
    obj.data = "/map/region/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] : 10000029) + "/";
    obj.type = "image/svg+xml";

    $("#intelRegion").appendChild(obj);//.children[0].data = "/map/region/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] : 10000029) + "/";

    cb();

}

function mapLoaded () {
    var obj = $("#intelRegion").children[0];
    var svg = obj.contentDocument;
    svg.documentElement.style.background = "transparent";

    var systems = svg.getElementsByClassName("sys");
    for(var s in systems) {
        if(typeof(systems[s]) == "object") {
            // set white bg
            var nod = svg.getElementById("rect" + systems[s].parentNode.id.replace("def", ""));
            if(nod && nod.style) nod.style.fill = "white";

            // set links
            var nam = systems[s].getAttribute("xlink:href").replace("http://evemaps.dotlan.net/system/","").replace("http://evemaps.dotlan.net/map/","");
            nam = nam.split("/");
            nam = nam[nam.length - 1];
            nam = nam.replace("_", " ");

            systems[s].setAttribute("xlink:href", "/#!/intel/system/" + systems[s].parentNode.id.replace("def", "") + "/");
        }
    }
    svg.documentElement.style.visibility = "hidden";
    setTimeout(function () { svg.documentElement.style.visibility = "visible"; }, 0);

    refreshDom();
    $("#intelRegion").className = "";

    setTimeout(checkRegionStatus, 1000);
}

var switchedIntel = false;
var flashInterval;

var systemStatus = {};
function checkSystemStatus () {
    if(!(location.hash.split("/")[1] == "intel" && location.hash.split("/")[2] == "system") || switchedIntel) {
        return;
    }
    systemAjax = ajax("/json/intel/system/" + (location.hash.split("/")[3] !== "" ? location.hash.split("/")[3] + "/" : "") + "?hash=" + md5(JSON.stringify(systemStatus)), function (r) {
        if(JSON.stringify(systemStatus) != JSON.stringify(r)) {
            systemStatus = r;

            clearInterval(flashInterval);
            $("#intelConti").className = "contentConti";
            if(systemStatus.state > 1)
                startFlashing(systemStatus.state);

            $("#intelStatus").innerHTML = "[" + systemStatus.status + "]";
            $("#intelLocation").innerHTML = systemStatus.regionName + " - " + systemStatus.systemName;

            $("#regionNav").children[0].href = "#!/intel/region/" + systemStatus.regionID + "/";

            if($("#checkTracker").checked) {
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
                            (systemStatus.membertype == "characters" ? new Date(systemStatus.members[j].timestamp * 1000).toJSON().split(".")[0] : ""), 
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

            var obj = $("#intelRegion").children[0];
            var svg = obj.contentDocument;
            svg.documentElement.style.background = "transparent";
            for(var i = 0; i < regionStatus.length; i++) {
                var el = svg.getElementById("def" + regionStatus[i].systemID);
                var s = svg.getElementById("rect" + regionStatus[i].systemID);
                if(s)
                    s.style.fill = calcColor(regionStatus[i].hostilecount, regionStatus[i].lastreport);
            }

            svg.documentElement.style.visibility = "hidden";
            setTimeout(function () { svg.documentElement.style.visibility = "visible"; }, 0);

        }

        setTimeout(checkRegionStatus, 100);
    }, "json");
}

function calcColor (hostilecount, timestamp) {
    if(hostilecount == 0) return "white";
    var maxage = 60 * 60 * 1;
    var d = new Date();
    var now = parseInt(d.getTime()/1000);
    var off = Math.max(timestamp - (now - maxage), 0) / maxage;
    return "rgb(255," + parseInt(255 - (255 * off)) + ",0)";
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
            humane.log("Intel submitted");
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

function startFlashing (state) {
    var states = ["offline", "online", "wakeup", "attention", "warning"];
    var audio = new Audio("/audio/" + states[state] + ".ogg");
    audio.play();

    var on = false;
    flashInterval = setInterval(function () {
        if(on) {
            $("#intelConti").className = "contentConti";
        } else {
            $("#intelConti").className = "contentConti intelStatus" + state;
        }
        on = !on;
    }, 500);
}