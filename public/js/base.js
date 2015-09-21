var loginurl = "https://login.eveonline.com/oauth/authorize?response_type=code&redirect_uri=http://core.eneticum.rep.pm/login/eve/&client_id=6fe200c8c9ef4ab59fe595e86de454af&scope=&state=";

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

function refreshDom () {
    if(typeof(CCPEVE) == "undefined") return;
    $('#container').style.visibility = 'hidden';
    setTimeout(function () {
        $('#container').style.visibility = 'visible';
    }, 0);
}

function click (elem) {
    if(typeof(CCPEVE) == "undefined") return;
    hashChange(elem);
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
                case "xml":
                    callback(xmlhttp.responseXML);
                default:
                    callback(xmlhttp.responseText);
                    break;
            }
        }
    };
    if(format == "xml")
        xmlhttp.overrideMimeType("image/svg+xml");
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
        return typeof args[0] != 'undefined' && typeof args[0][varname] != 'undefined' ? args[0][varname] : "";
    });
};

/**
 * Number.prototype.format(n, x, s, c)
 *
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};