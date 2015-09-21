window.onload = function () {
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

window.onhashchange = hashChange;


var oldHash = "";
function hashChange (elem) {
    $("#checkSidebar").checked = false;
    if(elem && elem.getAttribute) location.hash = elem.getAttribute("href");
    if(location.hash.slice(1) === "") location.hash = "#!/home/";
    $("#loginurl").href = loginurl + escape(location.pathname) + escape(location.search) + escape(location.hash);
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
                        var scriptelem = document.createElement("script");
                        scriptelem.src = $("#content script")[$("#content script").length - 1].src;
                        scriptelem.text = $("#content script")[$("#content script").length - 1].text;

                        $("#content").appendChild(scriptelem);
                        //setTimeout(function () { $("#content").removeChild(scriptelem); }, 1000);
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
    $("#charImg").src = "https://imageserver.eveonline.com/Character/" + charid + "_64.jpg";
    $("#charImg").alt = charname;
    $("#charName").innerHTML = charname;
    setCharList();
    setUnreadCnt();
}

function setUnreadCnt () {
    ajax("/json/notifications/unread/", function (r) {
      $("#unreadcounta").innerHTML = r.unread;
    }, "json");
}

function setCharList () {
    ajax("/json/characters/", function (r) {
        var el = $("#charlist");
        el.innerHTML = '';
        for (var i = r.length - 1; i >= 0; i--) {
            if(r[i].characterID == coreStatus.charid) continue;
            el.innerHTML += '<div class="hover row">' +
                '<img src="https://imageserver.eveonline.com/Character/' + r[i].characterID + '_32.jpg" alt="' + r[i].characterName + '"/>' +
                '<span onclick="switchCharacter(' + r[i].characterID + ');">' + r[i].characterName + '</span>' +
            '</div>';
        }
    }, "json");
}

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
            el.getElementsByClassName("notif")[0].innerHTML = '<div>' + r[i].subject + '</div>';
    }, "json");
}

function switchCharacter (charid) {
    if(charid == coreStatus.charid) return;
    ajax("/json/character/switch/" + charid + "/", function (r) {}, "json");
}

function splitPane () {
    document.body.className = "split";
    var fr = createElement('<div id="iframeParent"><iframe id="ifr" src="/" frameBorder="0"></iframe></div>');
    $("#splitPane").parentNode.insertBefore(fr, $("#splitPane"));
    $("#ifr").contentWindow.location.href = $("#ifr").src;
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
