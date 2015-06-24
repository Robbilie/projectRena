<?php

/* /pages/structures.twig */
class __TwigTemplate_a6e4f4aee0734d4ae8581b7196b5d12217956f4cd4dcfbdc5ee4a31008c6ff13 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"structuresConti\">
\t<div id=\"structuresNav\" class=\"navigation\">
\t\t<div id=\"controltowerNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/structures/controltower/\">Control Tower</a></div><div id=\"warpdisruptorNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/structures/warpdisruptor/\">Warp Disruptor</a></div><div id=\"sovereigntyNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/structures/sovereignty/\">Sovereignty</a></div><div id=\"personalNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/structures/personal/\">Personal</a></div>
\t</div>
\t<div id=\"structuresContent\" class=\"undernav\"></div>
\t<div style=\"display: none;\">
\t\t<table>
\t\t\t<tbody id=\"controltowerStructuresTemplate\">
\t\t\t\t<tr>
\t\t\t\t\t<td colspan=\"1\"><span class=\"info hover\" onmouseover=\"loadNotif({0}, this);\"><div class=\"notif\"></div>i</span><a href=\"#!/structures/controltower/{0}/\" onclick=\"click(this);\">{1}</a></td>
\t\t\t\t\t<td colspan=\"3\">{2}</td>
\t\t\t\t</tr>
\t\t\t\t<tr>
\t\t\t\t\t<td colspan=\"1\">Status: {3}</td>
\t\t\t\t\t<td colspan=\"3\">{4}</td>
\t\t\t\t</tr>
\t\t\t\t<tr><td>Fuel</td><td colspan=\"3\"><div class=\"progressBar\"><div style=\"width: {5}%;\"></div></div></td></tr>
\t\t\t\t<tr><td>Strontium</td><td colspan=\"3\"><div class=\"progressBar\"><div style=\"width: {6}%;\"></div></div></td></tr>
\t\t\t\t<tr class=\"ttr\"><td colspan=\"4\"></td></tr>
\t\t\t</tbody>
\t\t</table>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tstructuresJS();
\tfunction structuresJS () {
\t\tif(location.hash.split(\"/\")[2] == \"\") {
\t\t\tvar t = \$(\"#controltowerNav\").children[0];
\t\t\tlocation.hash = t.getAttribute(\"href\");
\t\t\tclick(t);
\t\t} else {
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className.replace(\" selected\", \"\");
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className += \" selected\";
\t\t\tvar fn = \"structures\" + location.hash.split(\"/\")[2] + \"JS\";
\t\t\tif(typeof window[fn] === \"function\")
\t\t\t\twindow[fn](function () {
\t\t\t\t\tfadeOn(\$(\"#structuresConti\"), 1);
\t\t\t\t});
\t\t}
\t}

\tfunction structurescontroltowerJS (cb) {
\t\tvar tmpl = \$(\"#controltowerStructuresTemplate\").innerHTML;
\t\tvar structCont = \$(\"#structuresContent\");
\t\tvar states = [\"Unanchored\", \"Anchored / Offline\", \"Onlining\", \"Reinforced\", \"Online\"];
\t\tajax(\"/json/structures/controltower/\", function (r) {
\t\t\tvar corp = \"\";
\t\t\tvar region = \"\";
\t\t\tvar system = \"\";
\t\t\tvar ctn = \"\";
\t\t\tvar chngd = false;
\t\t\tctn += '<table border=\"0\" class=\"quat\">';
\t\t\tctn += '<tr><th></th><th></th><th></th><th></th></tr>';
\t\t\tfor (var i = 0; i < r.length; i++) {
\t\t\t\tif(corp != r[i]['corpName']) {
\t\t\t\t\tcorp = r[i]['corpName'];
\t\t\t\t\tctn += '<h2 class=\"mtn mbn\">' + corp + '</h2>';
\t\t\t\t}
\t\t\t\tif(region != r[i]['regionName']) {
\t\t\t\t\tregion = r[i]['regionName'];
\t\t\t\t\tctn += '<tr><td colspan=\"4\"><h3 class=\"mtn mbn\">' + region + '</h3></td></tr>';
\t\t\t\t}
\t\t\t\tif(system != r[i]['solarSystemName']) {
\t\t\t\t\tsystem = r[i]['solarSystemName'];
\t\t\t\t\tctn += '<tr class=\"ttr\"><td colspan=\"4\"><h4 class=\"mtn mbn\">' + system + '</h3></td></tr>';
\t\t\t\t}
\t\t\t\tvar fuel = 0;
\t\t\t\tvar stront = 0;
\t\t\t\tfor(var j = 0; j < r[i]['content'].length; j++) {
\t\t\t\t\tif(r[i]['content'][j]['group'] == 1136) {
\t\t\t\t\t\tfuel += (r[i]['content'][j]['volume'] * r[i]['content'][j]['quantity']);
\t\t\t\t\t} else if(r[i]['content'][j]['typeID'] == 16275) {
\t\t\t\t\t\tstront += (r[i]['content'][j]['volume'] * r[i]['content'][j]['quantity']);
\t\t\t\t\t}
\t\t\t\t}
\t\t\t\tctn += tmpl.format(r[i]['id'], r[i]['name'], r[i]['moonName'], states[r[i]['state']], r[i]['typeName'], fuel / r[i]['capacity'] * 100, stront / r[i]['secondaryCapacity'] * 100);
\t\t\t\tchngd = false;
\t\t\t};
\t\t\tctn += '</table>'
\t\t\tstructCont.innerHTML = ctn;
\t\t\tcb();
\t\t}, \"json\");
\t}

\tfunction structureswarpdisruptorJS (cb) {
\t\tcb();
\t}

\tfunction structuressovereigntyJS (cb) {
\t\tcb();
\t}

\tfunction structurespersonalJS (cb) {
\t\tcb();
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/structures.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
