<?php

/* /pages/intel.twig */
class __TwigTemplate_47964f1dba91deda3d6fabc6c99f221a0c10e9ac36815a7f3e34ac5411fb12e2 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"intelConti\">
    <div id=\"intelNav\" class=\"navigation\">
        <div id=\"systemNav\" class=\"half\"><a onclick=\"click(this);\" href=\"#!/intel/system/\">System</a></div><div id=\"regionNav\" class=\"half\"><a onclick=\"click(this);\" href=\"#!/intel/region/\">Region</a></div>
    </div>
    <div id=\"intelContent\" class=\"undernav\">
        <div id=\"intelSystem\" class=\"hide\">
            <h1 id=\"intelStatus\" class=\"intelText mbn\">[Offline]</h1>
            <h3 id=\"intelLocation\" class=\"intelText mtn\"></h3>
            <div id=\"intelWarnings\"></div>
            <div id=\"intelMemberlist\"></div>
        </div>
        <div id=\"intelRegion\" class=\"hide\">
            <iframe id=\"regionFrame\" src=\"\"></iframe>
        </div>
    </div>
    <div style=\"display: none;\">
        <div id=\"charTemplate\">
            <div class=\"hover row {2}\"><img src=\"https://image.eveonline.com/Character/{0}_32.jpg\" alt=\"{1}\"/><a href=\"#!/profile/{0}/\" onclick=\"click(this);\">{1}</a></div>
        </div>
        <div id=\"alliTemplate\">
            <div class=\"hover row {2}\"><img src=\"https://image.eveonline.com/Alliance/{0}_32.png\" alt=\"{1}\"/><a href=\"#!/profile/{0}/\" onclick=\"click(this);\">{1}</a></div>
        </div>
    </div>
</div>
<div id=\"intelSettings\">
    <img src=\"./img/icons/Settings.png\" alt=\"Settings\"/>
    <div id=\"intelForm\" class=\"\">
        <input type=\"checkbox\" class=\"check\" id=\"checkTracker\" checked=\"true\" onchange=\"toggleTrack(this);refreshDom();\"/>
        <div id=\"intelTracking\">
            <h5 class=\"smt mbn\">System</h5>
            <input type=\"text\" id=\"intelSystemName\" class=\"mtn\" data-url=\"/json/systemnames/:param\"/>
        </div>
        <h5 class=\"smt mbn\">Paste</h5>
        <textarea id=\"intelPasteArea\" class=\"mtn\" onpaste=\"paste(event);\"></textarea>
        <label for=\"checkTracker\" id=\"intelTracker\">Track</label>
    </div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
    intelJS();
    function intelJS () {
        if(location.hash.split(\"/\")[2] == \"\") {
            var t = \$(\"#systemNav\").children[0];
            location.hash = t.getAttribute(\"href\");
            click(t);
        } else {
            \$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className.replace(\" selected\", \"\");
            \$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className += \" selected\";
            var fn = \"intel\" + location.hash.split(\"/\")[2] + \"JS\";
            if(typeof window[fn] === \"function\")
                window[fn](function () {
                    fadeOn(\$(\"#intelConti\"), 1);
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

        \$(\"#intelSystem\").className = \"\";
        if(location.hash.split(\"/\")[3] != \"\")
            \$(\"#checkTracker\").checked = false;

        dd = new AutoComplete(\"intelSystemName\");
        dd.oncomplete = function (self, el) {
            intelSystemID = el.getAttribute(\"data-dat\");
            if(intelSystemID != location.hash.split(\"/\")[3]) {
                location.hash = \"#!/intel/system/\" + intelSystemID + \"/\";
                click();
            }
        };

        checkSystemStatus();
        cb();
    }

    function intelregionJS (cb) {
        if(systemAjax)
            systemAjax.abort();

        \$(\"#intelRegion\").className = \"\";
        ajax(\"/map/region/\" + (location.hash.split(\"/\")[3] != \"\" ? location.hash.split(\"/\")[3] : 10000029) + \"/\", function (r) {
            \$(\"#regionFrame\").outerHTML = r;
            var svg = \$(\"#intelRegion\").children[0];
            svg.style.background = \"transparent\";

            var systems = svg.getElementsByClassName(\"sys\");
            for(var s in systems) {
                if(typeof(systems[s]) == \"object\") {
                    // set white bg
                    var r = systems[s].getElementsByClassName(\"s\")[0];
                    if(r && r.style) r.style.fill = \"white\";

                    // set links
                    var nam = systems[s].getAttribute(\"xlink:href\").replace(\"http://evemaps.dotlan.net/system/\",\"\").replace(\"http://evemaps.dotlan.net/map/\",\"\");
                    nam = nam.split(\"/\");
                    nam = nam[nam.length - 1];
                    nam = nam.replace(\"_\", \" \");
                    setNodeName(systems[s], nam);
                }
            }

            refreshDom();

        });
        
        checkRegionStatus();
        cb();
    }

    var switchedIntel = false;

    var systemStatus = {};
    function checkSystemStatus () {
        if(!(location.hash.split(\"/\")[1] == \"intel\" && location.hash.split(\"/\")[2] == \"system\") || switchedIntel) {
            return;
        }
        systemAjax = ajax(\"/json/intel/system/\" + (location.hash.split(\"/\")[3] != \"\" ? location.hash.split(\"/\")[3] + \"/\" : \"\") + \"?hash=\" + md5(JSON.stringify(systemStatus)), function (r) {
            if(JSON.stringify(systemStatus) != JSON.stringify(r)) {
                systemStatus = r;

                \$(\"#intelConti\").className = \"contentConti intelStatus\" + systemStatus;

                \$(\"#intelStatus\").innerHTML = \"[\" + systemStatus.status + \"]\";
                \$(\"#intelLocation\").innerHTML = systemStatus.regionName + \" - \" + systemStatus.systemName;

                \$(\"#regionNav\").children[0].href = \"#!/intel/region/\" + systemStatus.regionID + \"/\";

                if(\$(\"#intelSystemName\").value == \"\") {
                    \$(\"#intelSystemName\").value = systemStatus.systemName;
                    intelSystemID = systemStatus.systemID;
                }

                var tmpl = \$(\"#\" + (systemStatus.membertype == \"characters\" ? \"char\" : \"alli\") + \"Template\").innerHTML;
                var el = \$(\"#intelMemberlist\");
                el.innerHTML = \"\";
                for(var i = 0; i < systemStatus.members.length; i++)
                    el.innerHTML += tmpl.format(systemStatus.members[i].id, systemStatus.members[i].name + (systemStatus.members[i].count ? \" [\" + systemStatus.members[i].count + \"]\" : \"\"), systemStatus.members[i].standing + \"Standing\");
            }

            setTimeout(checkSystemStatus, 100);
        }, \"json\");
    }

    function checkRegionStatus () {
        if(!(location.hash.split(\"/\")[1] == \"intel\" && location.hash.split(\"/\")[2] == \"region\") || switchedIntel) {
            return;
        }
        regionAjax = ajax(\"/json/intel/region/\" + (location.hash.split(\"/\")[3] != \"\" ? location.hash.split(\"/\")[3] + \"/\" : \"\"), function (r) {
            console.log(r);
            setTimeout(checkRegionStatus, 10000);
        }, \"json\");
    }

    var submitting = false;
    function paste (e) {
        console.log(\"Pasted\");
        if (e.preventDefault) {
            e.stopPropagation();
            e.preventDefault();
        }
        if(submitting) return false;
        submitting = true;
        var dat = e.clipboardData.getData('text/plain');
        dat = dat.split(\"\\n\");
        submitIntel(dat);
    }

    function submitIntel (dat) {
        var req = new XMLHttpRequest();
        req.onreadystatechange = function () {
            if(req.readyState == 4 && req.status == 200) {
                alert(\"Submitted\");
                submitting = false;
            }
        };
        req.open(\"POST\", \"/json/intel/system/\" + (!intelSystemID ? \"\" : intelSystemID + \"/\"), true);
        req.setRequestHeader(\"Content-type\",\"application/x-www-form-urlencoded\");
        req.send(encodeURI(\"local=\" + dat.join(\",\")));
    }

    function toggleTrack (el) {
        if(el.checked) {
            location.hash = \"#!/intel/system/\";
                click();
        }
    }

    function setNodeName (node, name) {
        ajax(\"/json/systemnames/\" + name, function (r) {
            if(!r[0]) {
                console.debug(r);
                console.debug(name);
            }
            if(r.length > 0)
                node.setAttribute(\"xlink:href\", \"/#!/intel/system/\" + r[0].data + \"/\");
        }, \"json\");
    }
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/intel.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
