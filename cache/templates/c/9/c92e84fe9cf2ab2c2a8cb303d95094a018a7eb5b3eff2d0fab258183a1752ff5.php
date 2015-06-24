<?php

/* /pages/members.twig */
class __TwigTemplate_c92e84fe9cf2ab2c2a8cb303d95094a018a7eb5b3eff2d0fab258183a1752ff5 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"membersConti\">
\t<div id=\"membersNav\" class=\"navigation\">
\t\t<div id=\"corporationNav\" class=\"half\"><a onclick=\"click(this);\" href=\"#!/members/corporation/\">Corporation</a></div><div id=\"allianceNav\" class=\"half\"><a onclick=\"click(this);\" href=\"#!/members/alliance/\">Alliance</a></div>
\t</div>
\t<div id=\"membersContent\" class=\"undernav\">
\t\t<div class=\"card\">
\t\t\t<div class=\"chead\">
\t\t\t\t<h2 class=\"blue\" id=\"memberTitle\"></h2>
\t\t\t</div>
\t\t\t<div class=\"cbody\">
\t\t\t\t<div id=\"corporationList\"></div>
\t\t\t\t<div id=\"allianceList\"></div>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div style=\"display: none;\">
\t\t<div id=\"memberTemplate\">
\t\t\t<div class=\"hover row\"><a href=\"#!/profile/{0}/\" onclick=\"click(this);\">{1}</a>{2}</div>
\t\t</div>
\t\t<div id=\"corporationTemplate\">
\t\t\t<div class=\"hover row\"><a href=\"#!/members/corporation/{0}/\" onclick=\"click(this);\">{1}</a>{2}</div>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tmembersJS();
\tfunction membersJS () {
\t\tif(location.hash.split(\"/\")[2] == \"\") {
\t\t\tvar t = \$(\"#coporationNav\").children[0];
\t\t\tlocation.hash = t.getAttribute(\"href\");
\t\t\tclick(t);
\t\t} else {
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className.replace(\" selected\", \"\");
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className += \" selected\";
\t\t\tvar fn = \"members\" + location.hash.split(\"/\")[2] + \"JS\";
\t\t\tif(typeof window[fn] === \"function\")
\t\t\t\twindow[fn](function () {
\t\t\t\t\tfadeOn(\$(\"#membersConti\"), 1);
\t\t\t\t});
\t\t}
\t}

\tfunction memberscorporationJS (cb) {
\t\tif(location.hash.split(\"/\")[3] == \"\") {
\t\t\tajax(\"/json/character/\" + coreStatus.charid + \"/\", function (r) {
\t\t\t\tlistCorpMembers(r.corporationID, cb);
\t\t\t}, \"json\");
\t\t} else {
\t\t\tlistCorpMembers(location.hash.split(\"/\")[3], cb);
\t\t}
\t}

\tfunction listCorpMembers (id, cb) {
\t\tajax(\"/json/corporation/\" + id + \"/\", function (r) {
\t\t\t\$(\"#memberTitle\").innerHTML = r.name;
\t\t\tajax(\"/json/corporation/\" + id + \"/members/\", function (s) {
\t\t\t\tvar tmpl = \$(\"#memberTemplate\").innerHTML;
\t\t\t\tvar el = \$(\"#corporationList\");
\t\t\t\tel.innerHTML = \"\";
\t\t\t\tfor(var i = 0; i < s.length; i++)
\t\t\t\t\tel.innerHTML += tmpl.format(s[i].characterID, s[i].characterName, '');
\t\t\t\tcb();
\t\t\t}, \"json\");
\t\t}, \"json\");
\t\t
\t}

\tfunction membersallianceJS (cb) {
\t\tif(location.hash.split(\"/\")[3] == \"\") {
\t\t\tajax(\"/json/character/\" + coreStatus.charid + \"/\", function (r) {
\t\t\t\tlistAlliMembers(r.allianceID, cb);
\t\t\t}, \"json\");
\t\t} else {
\t\t\tlistAlliCorps(location.hash.split(\"/\")[3], cb);
\t\t}
\t}

\tfunction listAlliMembers (id, cb) {
\t\tajax(\"/json/alliance/\" + id + \"/\", function (r) {
\t\t\t\$(\"#memberTitle\").innerHTML = r.name;
\t\t\tajax(\"/json/alliance/\" + id + \"/members/\", function (s) {
\t\t\t\tvar tmpl = \$(\"#memberTemplate\").innerHTML;
\t\t\t\tvar el = \$(\"#allianceList\");
\t\t\t\tel.innerHTML = \"\";
\t\t\t\tfor(var i = 0; i < s.length; i++)
\t\t\t\t\tel.innerHTML += tmpl.format(s[i].characterID, s[i].characterName, '');
\t\t\t\tcb();
\t\t\t}, \"json\");
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/members.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
