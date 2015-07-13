var loginurl = "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=http://core.eneticum.rep.pm/login/eve/&client_id=6fe200c8c9ef4ab59fe595e86de454af&scope=&state=/";

function $ (id, el) {
    var c = el ? el : document;
    if(document.querySelector && document.querySelectorAll) {
        if(id.substr(0,1) == "#" && id.split(" ").length == 1) {
            return document.querySelector(id);
        } else {
            return document.querySelectorAll(id);
        }
    } else {
        switch(id.substr(0,1)) {
            case "#":
                return c.getElementById(id.slice(1));
            case ".":
                return c.getElementsByClassName(id.slice(1));
            default:
                return c.getElementsByTagName(id);
        }
    }
}
function createElement (elStr) {
    var tmpEl = document.createElement("div");
    tmpEl.innerHTML = elStr;
    for(var i = 0; i < tmpEl.children.length; i++)
      if(tmpEl.children[0].nodeType == 1)
        return tmpEl.children[i];
}

window.onload = function () {
  if(window != window.top)
    document.body.className = "iframe";
  checkStatus(); setTimeout(hashChange, 300);
};
window.onhashchange = hashChange;

function click (elem) {
    if(typeof(CCPEVE) == "undefined") return;
    hashChange(elem);
}

var oldHash = "";
function hashChange (elem) {
    $("#checkSidebar").checked = false;
    if(elem && elem.getAttribute) location.hash = elem.getAttribute("href");
    if(location.hash.slice(1) === "") location.hash = "#!/home/";
    $("#loginurl").href = loginurl + escape(location.hash);
    if(location.hash.split("/")[1]) {
        ajax(location.hash.slice(2), function (r) {
            if(location.hash.split("/")[1] == "logout") location.hash = "#!/home/";
            setTimeout(function () {
                if($(".contentConti")[0])
                    $(".contentConti")[0].style.opacity = 0;
                setTimeout(function () {
                    // inject new content
                    $("#content").innerHTML = r;
                    // js exec magic
                    if($("#content script").length > 0) {
                        console.log("script");

                        var scriptelem = document.createElement("script");
                        scriptelem.src = $("#content script")[0].src;
                        scriptelem.text = $("#content script")[0].text;

                        $("#content").appendChild(scriptelem);
                        $("#content").removeChild(scriptelem);
                    } else {
                        console.log("no script");
                        if($(".contentConti")[0])
                            fadeOn($(".contentConti")[0], 1);
                    }
                }, 200);
            }, 10);
        });
        if(location.hash.split("/")[1] != "logout" && oldHash != location.hash.split("/")[1]) {
            $("#title").innerHTML += "<div>" + location.hash.split("/")[1] + "</div>";
            if($("#titleflow").style.marginTop === "")
                $("#titleflow").style.marginTop = "0px";
            setTimeout(function () {
                $("#titleflow").style.marginTop = (parseInt($("#titleflow").style.marginTop) - 70) + "px";
                setTimeout(function () {
                    $("#title").innerHTML = '<div id="titleflow" style="margin-top: -70px;">&nbsp;</div><div>' + location.hash.split("/")[1] + '</div>';
                    oldHash = location.hash.split("/")[1];
                }, 200);
            }, 10);
        }
    }
}

function refreshDom () {
    if(typeof(CCPEVE) == "undefined") return;
    $('#container').style.visibility = 'hidden';
    setTimeout(function () {
        $('#container').style.visibility = 'visible';
    }, 0);
}

var coreStatus = {};

function checkStatus (cb, poll) {
    ajax("/json/status/?hash=" + md5(JSON.stringify(coreStatus)), function (r) {
        $("#checkLoggedin").checked = !(!r.isLoggedin);
        if(r.isLoggedin) {
            setLoggedinCard(r.charid, r.charname);
        }
        setTimeout(function () {
            checkStatus(null, poll);
        }, 1000);
        if(coreStatus.charid != r.charid)
          hashChange();
        coreStatus = r;
        refreshDom();
        if(cb)
            cb();
    }, "json", { retry: true });
}

function setLoggedinCard (charid, charname) {
    $("#charImg").src = "https://image.eveonline.com/Character/" + charid + "_64.jpg";
    $("#charImg").alt = charname;
    $("#charName").innerHTML = charname;
    ajax("/json/characters/", function (r) {
        var el = $("#charlist");
        el.innerHTML = '';
        for (var i = r.length - 1; i >= 0; i--) {
            if(r[i].characterID == charid) continue;
            el.innerHTML += '<div class="hover row">' +
                '<img src="https://image.eveonline.com/Character/' + r[i].characterID + '_32.jpg" alt="' + r[i].characterName + '"/>' +
                '<span onclick="switchCharacter(' + r[i].characterID + ');">' + r[i].characterName + '</span>' +
            '</div>';
        }
    }, "json");
    ajax("/json/notifications/unread/", function (r) {
      $("#unreadcounta").innerHTML = r.unread;
    }, "json");
}

function ajax (url, callback, format, options) {
    var timeout;
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            clearTimeout(timeout);
            switch(format) {
                case "json":
                    callback(JSON.parse(xmlhttp.responseText));
                    break;
                default:
                    callback(xmlhttp.responseText);
                    break;
            }
        }
    };
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    timeout = setTimeout(function () {
        xmlhttp.abort();
        console.log("Request Timed out");
        if(options && options.retry === true)
            ajax(url, callback, format, options);
    }, options && options.timeout ? options.timeout : 30000);
    return xmlhttp;
}

function fadeOn (el, op) {
    setTimeout(function () {
        el.style.opacity = op;
    }, 10);
}

String.prototype.format = function() {
    var args = arguments;
    return this.replace(/{([a-zA-Z\d]+)}/g, function(match, varname) {
        return typeof args[0] != 'undefined' && args[0][varname] ? args[0][varname] : "";
    });
};

function loadNotif (id, el) {
    if(el.getElementsByClassName("notif")[0].innerHTML === "") {
        el.getElementsByClassName("notif")[0].innerHTML =  "Loading...";
        getNotif(id, el);
    }
}

function getNotif (id, el) {
    ajax("/json/notifications/" + id + "/", function (r) {
      if(!r) return;
        el.getElementsByClassName("notif")[0].innerHTML = "";
        for(var i = 0; i < r.length; i++)
            el.getElementsByClassName("notif")[0].innerHTML = '<div>' + r[i].text + '</div>';
    }, "json");
}

function switchCharacter (charid) {
    if(charid == coreStatus.charid) return;
    ajax("/json/character/switch/" + charid + "/", function (r) {}, "json");
}

function splitPane () {
  document.body.className = "split";
  var fr = createElement('<div id="iframeParent"><iframe src="/" frameBorder="0"></iframe></div>');
  $("#splitPane").parentNode.insertBefore(fr, $("#splitPane"));
  $("#splitPane").setAttribute("onclick", "removePane();");
  $("#splitPane").innerHTML = "-";
}

function removePane () {
  document.body.className = "";
  $("#container").removeChild($("#iframeParent"));
  $("#splitPane").setAttribute("onclick", "splitPane();");
  $("#splitPane").innerHTML = "+";
}

// autocomplete test

var AutoComplete = function (acEl) {
    var self = this;

    var oldel = acEl;
    this.url = oldel.getAttribute("data-url");
    var id = oldel.id;

    var dropdown = document.createElement("div");
    dropdown.className = "dropdowntf";

    this.input = createElement(oldel.outerHTML);
    this.input.addEventListener("keyup", function (event) {
        self.pollAutoComplete(self, event);
    });
    this.input.addEventListener("blur", function (event) {
        setTimeout(function () {
            self.dropdownConti.innerHTML = "";
        }, 200);
    });

    this.dropdownConti = createElement('<div class="dropdown"></div>');

    dropdown.appendChild(this.input);
    dropdown.appendChild(this.dropdownConti);

    oldel.parentNode.insertBefore(dropdown, oldel.nextSibling);
    oldel.parentNode.removeChild(oldel);

};

AutoComplete.prototype.pollAutoComplete = function (self, event) {
    if(self.input.value !== "")
      if(self.poll)
        self.poll.abort();
      self.poll = ajax(self.url.replace(":param", self.input.value), function (r) {
            var conti = self.dropdownConti;
            conti.innerHTML = "";
            for(var i = 0; i < r.length; i++) {
                var tmpdiv = createElement('<div class="hover row" data-dat="' + r[i].data + '">' + r[i].name + '</div>');
                self.addClickComplete(self, tmpdiv);
                conti.appendChild(tmpdiv);
            }
            if(r[0] && self.input.value == r[0].name)
                self.autoComplete(self, conti.children[0]);
        }, "json");
};

AutoComplete.prototype.addClickComplete = function (self, el) {
  el.addEventListener("click", function (event) {
    self.autoComplete(self, event.target);
  });
};

AutoComplete.prototype.autoComplete = function (self, el) {
    self.input.value = el.innerHTML;
    self.dropdownConti.innerHTML = "";

    if(self.oncomplete)
        self.oncomplete(self, el);
};
