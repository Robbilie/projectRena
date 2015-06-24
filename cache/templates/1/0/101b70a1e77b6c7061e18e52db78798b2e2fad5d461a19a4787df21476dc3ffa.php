<?php

/* index.twig */
class __TwigTemplate_101b70a1e77b6c7061e18e52db78798b2e2fad5d461a19a4787df21476dc3ffa extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\"/>
        <meta name=\"mobile-web-app-capable\" content=\"yes\">
        <title>Alliance - Services</title>
        <meta name=\"viewport\" content=\"width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0\">
        <link rel=\"stylesheet\" type=\"text/css\" href=\"./css/mock.css\">
        <script type=\"text/javascript\" src=\"/js/md5.min.js\"></script>
    </head>
    <body>
        <!-- #Toggles -->
        <input type=\"checkbox\" class=\"check\" id=\"checkLoggedin\" onchange=\"refreshDom();\" />
        <input type=\"checkbox\" class=\"check\" id=\"checkSidebar\"  onchange=\"refreshDom();\" />
        <input type=\"checkbox\" class=\"check\" id=\"checkMaxed\"    onchange=\"refreshDom();\" />
        <!-- #Content -->
        <div id=\"container\">
            <div id=\"header\">
                <div id=\"righthead\"></div>
                <div id=\"midhead\"></div>
                <div id=\"lefthead\"><label for=\"checkSidebar\" id=\"navigationBtn\">Navigation</label></div>
            </div>
            <h1 id=\"title\"><div id=\"titleflow\" style=\"\">&nbsp;</div></h1>
            <div id=\"sidebar\">
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/home/\">
                    <h2>Home</h2>
                    <img src=\"./img/icons/agent.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/profile/\">
                    <h2>Profile</h2>
                    <img src=\"./img/icons/charactersheet.png\"/>
                </a>
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/notifications/\">
                    <h2>Notifications</h2>
                    <img src=\"./img/icons/attention.png\"/>
                </a>-->
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/mails/\">
                    <h2>Mails</h2>
                    <img src=\"./img/icons/evemail.png\"/>
                </a>-->
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/intel/system/\">
                    <h2>Intel</h2>
                    <img src=\"./img/icons/chatchannel.png\"/>
                </a>
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/logistic/\">
                    <h2>Logistic</h2>
                    <img src=\"./img/icons/contracts.png\"/>
                </a>-->
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/corporation/\">
                    <h2>Corporation</h2>
                    <img src=\"./img/icons/corporation.png\"/>
                </a>-->
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/alliance/\">
                    <h2>Alliance</h2>
                    <img src=\"./img/icons/alliances.png\"/>
                </a>-->
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/structures/controltower/\">
                    <h2>Structures</h2>
                    <img src=\"./img/icons/station.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/assets/personal/\">
                    <h2>Assets</h2>
                    <img src=\"./img/icons/items.png\"/>
                </a>
                <!--<a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/fittings/\">
                    <h2>Fittings</h2>
                    <img src=\"./img/icons/fitting.png\"/>
                </a>-->
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/members/corporation/\">
                    <h2>Members</h2>
                    <img src=\"./img/icons/member.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/groups/\">
                    <h2>Groups</h2>
                    <img src=\"./img/icons/grouplist.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/settings/\">
                    <h2>Settings</h2>
                    <img src=\"./img/icons/Settings.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/help/\">
                    <h2>Help</h2>
                    <img src=\"./img/icons/help.png\"/>
                </a>
                <a class=\"sidebarrow\" onclick=\"click(this);\" href=\"#!/about/\">
                    <h2>About</h2>
                    <img src=\"./img/icons/info.png\"/>
                </a>
            </div>
            <div id=\"content\"></div>
            <div id=\"mobilebg\"></div>
            <div id=\"logincard\">
                <div id=\"loggedincard\">
                    <div id=\"cardbg\"></div>
                    <img id=\"charImg\" src=\"\" alt=\"\"><span id=\"charName\"></span>
                    <label id=\"logoutlabel\"><a href=\"#!/logout/\" onclick=\"click(this);\">Logout</a></label>
                    <label id=\"switchlabel\">Switch Character</label>
                    <div id=\"charlist\"></div>
                </div>
                <div id=\"loggedoutcard\">
                    <img src=\"./img/4_128_1.png\"/>
                    <h1>Alliance Services</h1>
                    <a id=\"loginurl\" href=\"";
        // line 103
        echo twig_escape_filter($this->env, (isset($context["EVESSOURL"]) ? $context["EVESSOURL"] : null), "html", null, true);
        echo "\" onclick=\"/*if(typeof(CCPEVE) == 'undefined') { authwin = window.open(this.href, 'mywin','left=200,top=200,width=500,height=600,toolbar=1,resizable=0'); return false;}*/\"><img src=\"/img/EVE_SSO_Login_Buttons_Small_White.png\" alt=\"EVE SSO Login Button\"></a>
                    <div id=\"disclaimer\" class=\"abs\">
                        <span class=\"info hover\">
                            <div class=\"notif abs\">
                                <p style=\"font-size:80%\" class=\"paddedp\">
                                    Learn more about the security of <i>EVE Online SSO</i> in this <a href=\"http://community.eveonline.com/news/dev-blogs/eve-online-sso-and-what-you-need-to-know/\" target=\"_blank\">dev-blog</a> article by <a href=\"https://twitter.com/CCP_FoxFour\" target=\"_blank\">CCP FoxFour</a>.<br>
                                    Summary: Verify that after clicking the login button, you are redirected to https://login.eveonline.com/ and it <a href=\"img/ccp_hf_cert.png\" target=\"_blank\">shows up</a> as certified by <i>CCP Hf [IS]</i>.
                                </p>
                            </div>
                            i
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <script type=\"text/javascript\">
            var authwin;
            var loginurl = \"";
        // line 120
        echo twig_escape_filter($this->env, twig_escape_filter($this->env, (isset($context["EVESSOURL"]) ? $context["EVESSOURL"] : null), "js"), "html", null, true);
        echo "\";

            var \$ = function (id, el) {
                var c = el ? el : document; 
                switch(id.substr(0,1)) {
                    case \"#\":
                        return c.getElementById(id.slice(1));
                        break;
                    case \".\":
                        return c.getElementsByClassName(id.slice(1));
                        break;
                    default:
                        return c.getElementsByTagName(id);
                        break;
                }
            };
            window.onload = function () { checkStatus(); setTimeout(hashChange, 300); };
            window.onhashchange = hashChange;

            function click (elem) {
                if(typeof(CCPEVE) == \"undefined\") return;
                hashChange(elem);
            }

            var oldHash = \"\";
            function hashChange (elem) {
                \$(\"#checkSidebar\").checked = false;
                if(elem && elem.getAttribute) location.hash = elem.getAttribute(\"href\");
                if(location.hash.slice(1) == \"\") location.hash = \"#!/home/\";
                \$(\"#loginurl\").href = loginurl + escape(location.hash);
                if(location.hash.split(\"/\")[1]) {
                    ajax(location.hash.slice(2), function (r) {
                        if(location.hash.split(\"/\")[1] == \"logout\") location.hash = \"#!/home/\";
                        setTimeout(function () {
                            if(document.getElementsByClassName(\"contentConti\")[0])
                                document.getElementsByClassName(\"contentConti\")[0].style.opacity = 0;
                            setTimeout(function () {
                                // inject new content
                                \$(\"#content\").innerHTML = r;
                                // js exec magic
                                if(\$(\"#content\").getElementsByTagName(\"script\").length > 0) {
                                    console.log(\"script\");
                                    var scripttxt = \$(\"#content\").getElementsByTagName(\"script\")[0].text;
                                    var scriptelem = document.createElement(\"script\");
                                    scriptelem.text = scripttxt;
                                    \$(\"#content\").appendChild(scriptelem);
                                    \$(\"#content\").removeChild(scriptelem);
                                } else {
                                    console.log(\"no script\");
                                    if(document.getElementsByClassName(\"contentConti\")[0])
                                        fadeOn(document.getElementsByClassName(\"contentConti\")[0], 1);
                                }
                            }, 200);
                        }, 10);
                    });
                    if(location.hash.split(\"/\")[1] != \"logout\" && oldHash != location.hash.split(\"/\")[1]) {
                        \$(\"#title\").innerHTML += \"<div>\" + location.hash.split(\"/\")[1] + \"</div>\";
                        if(\$(\"#titleflow\").style.marginTop == \"\")
                            \$(\"#titleflow\").style.marginTop = \"0px\";
                        setTimeout(function () {
                            \$(\"#titleflow\").style.marginTop = (parseInt(\$(\"#titleflow\").style.marginTop) - 70) + \"px\";
                            setTimeout(function () {
                                \$(\"#title\").innerHTML = '<div id=\"titleflow\" style=\"margin-top: -70px;\">&nbsp;</div><div>' + location.hash.split(\"/\")[1] + '</div>';
                                oldHash = location.hash.split(\"/\")[1];
                            }, 200);
                        }, 10);
                    }
                }
            }

            function refreshDom () {
                if(typeof(CCPEVE) == \"undefined\") return;
                document.getElementById('container').style.visibility = 'hidden';
                setTimeout(function () {
                    document.getElementById('container').style.visibility = 'visible';
                }, 0);
            }

            var coreStatus = {\"isLoggedin\":";
        // line 198
        if (((isset($context["LoggedIN"]) ? $context["LoggedIN"] : null) == 1)) {
            echo "true";
        } else {
            echo "false";
        }
        echo ",\"isAdmin\":false,\"charname\":\"";
        echo twig_escape_filter($this->env, (isset($context["characterName"]) ? $context["characterName"] : null), "html", null, true);
        echo "\",\"charid\":";
        echo twig_escape_filter($this->env, ((array_key_exists("characterID", $context)) ? (_twig_default_filter((isset($context["characterID"]) ? $context["characterID"] : null), 0)) : (0)), "html", null, true);
        echo "};
            //var statusInt = setTimeout(checkStatus, 60000);

            function checkStatus (cb, poll) {
                ajax(\"/json/status/?hash=\" +md5(JSON.stringify(coreStatus)), function (r) {
                    \$(\"#checkLoggedin\").checked = !(!r.isLoggedin);
                    if(r.isLoggedin) {
                        if(r.isLoggedin != coreStatus.isLoggedin)
                            hashChange();
                        setLoggedinCard(r.charid, r.charname);
                    }
                    setTimeout(function () {
                        checkStatus(null, poll);
                    }, 1000);
                    coreStatus = r;
                    refreshDom();
                    if(cb)
                        cb();
                }, \"json\", { retry: true });
            }

            function setLoggedinCard (charid, charname) {
                \$(\"#charImg\").src = \"https://image.eveonline.com/Character/\" + charid + \"_64.jpg\";
                \$(\"#charImg\").alt = charname;
                \$(\"#charName\").innerHTML = charname;
                ajax(\"/json/characters/\", function (r) {
                    var el = \$(\"#charlist\");
                    el.innerHTML = '';
                    for (var i = r.length - 1; i >= 0; i--) {
                        if(r[i]['characterID'] == charid) continue;
                        el.innerHTML += '<div class=\"hover row\">' + 
                            '<img src=\"https://image.eveonline.com/Character/' + r[i]['characterID'] + '_32.jpg\" alt=\"' + r[i]['characterName'] + '\"/>' + 
                            '<span onclick=\"switchCharacter(' + r[i]['characterID'] + ');\">' + r[i]['characterName'] + '</span>' + 
                        '</div>';
                    };
                }, \"json\");
            }

            function ajax (url, callback, format, options) {
                var timeout;
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                        clearTimeout(timeout);
                        switch(format) {
                            case \"json\":
                                callback(JSON.parse(xmlhttp.responseText));
                                break;
                            default:
                                callback(xmlhttp.responseText);
                                break;
                        }
                    }
                };
                xmlhttp.open(\"GET\", url, true);
                xmlhttp.send();
                timeout = setTimeout(function () {
                    xmlhttp.abort();
                    console.log(\"Request Timed out\");
                    if(options && options.retry == true)
                        ajax(url, callback, format, options);
                }, 30000);
                return xmlhttp;
            }

            function fadeOn (el, op) {
                setTimeout(function () {
                    el.style.opacity = op;
                }, 10);
            }

            String.prototype.format = function() {
                var args = arguments;
                return this.replace(/{(\\d+)}/g, function(match, number) { 
                    return typeof args[number] != 'undefined'
                        ? args[number]
                        : match
                    ;
                });
            };
            String.prototype.hashCode = function() {
                var hash = 0, i, chr, len;
                if (this.length == 0) return hash;
                for (i = 0, len = this.length; i < len; i++) {
                    chr   = this.charCodeAt(i);
                    hash  = ((hash << 5) - hash) + chr;
                    hash |= 0; // Convert to 32bit integer
                }
                return hash;
            };

            function loadNotif (id, el) {
                if(el.getElementsByClassName(\"notif\")[0].innerHTML == \"\") {
                    el.getElementsByClassName(\"notif\")[0].innerHTML =  \"Loading...\";
                    getNotif(id, el);
                }
            }

            function getNotif (id, el) {
                ajax(\"/json/notifications/\" + id + \"/\", function (r) {
                    el.getElementsByClassName(\"notif\")[0].innerHTML = \"\";
                    for(var i = 0; i < r.length; i++)
                        el.getElementsByClassName(\"notif\")[0].innerHTML = '<div>' + r[i].text + '</div>';
                }, \"json\");
            }

            function switchCharacter (charid) {
                if(charid == coreStatus.charid) return;
                ajax(\"/json/character/switch/\" + charid + \"/\", function (r) {
                    if(r.state == \"success\")
                        hashChange();
                }, \"json\");
            }

            // autocomplete test

            var AutoComplete = function (elname) {
                var self = this;

                var oldel = \$(\"#\" + elname);
                this.url = oldel.getAttribute(\"data-url\");
                var id = oldel.id;

                var dropdown = document.createElement(\"div\");
                dropdown.className = \"dropdowntf\";
                
                this.input = document.createElement(\"input\");
                this.input.className = oldel.className;
                this.input.type = \"text\";
                this.input.id = id;
                this.input.addEventListener(\"keyup\", function (event) {
                    self.pollAutoComplete(self, event);
                });
                this.input.addEventListener(\"blur\", function (event) {
                    setTimeout(function () {
                        self.dropdownConti.innerHTML = \"\";
                    }, 200);
                });

                this.dropdownConti = document.createElement(\"div\");
                this.dropdownConti.className = \"dropdown\";

                dropdown.appendChild(this.input);
                dropdown.appendChild(this.dropdownConti);

                oldel.parentNode.insertBefore(dropdown, oldel.nextSibling);
                oldel.parentNode.removeChild(oldel);

            };

            AutoComplete.prototype.pollAutoComplete = function (self, event) {
                if(self.input.value != \"\")
                    ajax(self.url.replace(\":param\", self.input.value), function (r) {
                        var conti = self.dropdownConti;
                        conti.innerHTML = \"\";
                        for(var i = 0; i < r.length; i++) {
                            var tmpdiv = document.createElement(\"div\");
                            tmpdiv.className = \"hover row\";
                            tmpdiv.innerHTML = r[i].name;
                            tmpdiv.setAttribute(\"data-dat\", r[i].data);
                            tmpdiv.addEventListener(\"click\", function (event) {
                                self.autoComplete(self, event.target);
                            });
                            conti.appendChild(tmpdiv);
                        }
                        if(r[0] && self.input.value == r[0].name)
                            self.autoComplete(self, conti.children[0]);
                    }, \"json\");
            };

            AutoComplete.prototype.autoComplete = function (self, el) {
                self.input.value = el.innerHTML;
                self.dropdownConti.innerHTML = \"\";

                if(self.oncomplete)
                    self.oncomplete(self, el);
            };

        </script>
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-64437654-1', 'auto');
            ga('send', 'pageview');

        </script>
    </body>
</html>
";
    }

    public function getTemplateName()
    {
        return "index.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  224 => 198,  143 => 120,  123 => 103,  19 => 1,);
    }
}
