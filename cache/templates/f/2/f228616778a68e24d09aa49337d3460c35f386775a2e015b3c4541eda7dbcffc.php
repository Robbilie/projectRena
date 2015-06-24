<?php

/* /pages/groups.twig */
class __TwigTemplate_f228616778a68e24d09aa49337d3460c35f386775a2e015b3c4541eda7dbcffc extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"groupsConti\">
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Editable Groups</h2>
\t\t</div>
\t\t<div id=\"groupslist\" class=\"cbody\">
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Corporation Groups</h2>
\t\t</div>
\t\t<div id=\"corporationGroupslist\" class=\"cbody\">
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Alliance Groups</h2>
\t\t</div>
\t\t<div id=\"allianceGroupslist\" class=\"cbody\">
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Create Group</h2>
\t\t</div>
\t\t<div id=\"groupform\" class=\"cbody paddedp\">
\t\t\t<h4 id=\"createresponse\"></h4>
\t\t\t<h4 class=\"mbn\">Group Name</h4>
\t\t\t<input type=\"text\" id=\"groupName\"/>
\t\t\t<h4 class=\"mbn\">Group Scope</h4>
\t\t\t<select id=\"groupScope\">
\t\t\t\t<option>corporation</option>
\t\t\t\t<option>alliance</option>
\t\t\t</select>
\t\t\t<h4 class=\"mbn\">Group State</h4>
\t\t\t<div class=\"hover row\">
\t\t\t\t<input type=\"checkbox\" id=\"groupState\" checked=\"true\" /><label for=\"groupState\">Is Private?</label>
\t\t\t</div>
\t\t\t<div class=\"divider\"></div>
\t\t\t<span class=\"btn\" onclick=\"createGroup();\">Create</span>
\t\t</div>
\t</div>
\t<div style=\"display: none;\">
\t\t<div id=\"groupsTemplate\">
\t\t\t<div class=\"hover paddedp\"><a href=\"#!/group/{0}/\" onclick=\"click(this);\">{1}</a>{2}</div>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tgroupsJS();
\tfunction groupsJS () {
\t\tajax(\"/json/groups/\", function (r) {
\t\t\tconsole.log(r);
\t\t\tvar tmpl = \$(\"#groupsTemplate\").innerHTML;
\t\t\tvar el1 = \$(\"#groupslist\");
\t\t\tvar el2 = \$(\"#corporationGroupslist\");
\t\t\tvar el3 = \$(\"#allianceGroupslist\");
\t\t\tel1.innerHTML = \"\";
\t\t\tel2.innerHTML = \"\";
\t\t\tel3.innerHTML = \"\";
\t\t\tfor(var i = 0; i < r.owned.length; i++)
\t\t\t\tif(r.owned[i].owner != null || (r.owned[i].owner == null && coreStatus.isAdmin))
\t\t\t\t\tel1.innerHTML += tmpl.format(r.owned[i].id,(r.owned[i].custom == 1 ? '[custom] ' : '') + r.owned[i].name, \"\");
\t\t\tfor(var i = 0; i < r.corporation.length; i++)
\t\t\t\tel2.innerHTML += tmpl.format(r.corporation[i].id,(r.corporation[i].custom == 1 ? '[custom] ' : '') + r.corporation[i].name, '<span class=\"fr\">[' + ((r.groups & Math.pow(2, r.corporation[i].id)) == Math.pow(2, r.corporation[i].id) ? \"member\" : \"apply\") + ']</span>');
\t\t\tfor(var i = 0; i < r.alliance.length; i++)
\t\t\t\tel3.innerHTML += tmpl.format(r.alliance[i].id,(r.alliance[i].custom == 1 ? '[custom] ' : '') + r.alliance[i].name, '<span class=\"fr\">[' + ((r.groups & Math.pow(2, r.alliance[i].id)) == Math.pow(2, r.alliance[i].id) ? \"member\" : \"apply\") + ']</span>');

\t\t\tfadeOn(\$(\"#groupsConti\"), 1);
\t\t}, \"json\");
\t}

\tfunction createGroup () {
\t\tajax(\"/json/group/create/\" + \$(\"#groupName\").value + \"/\" + \$(\"#groupScope\").value + \"/\" + \$(\"#groupState\").checked + \"/\", function (r) {
\t\t\tif(r.state == \"success\") {
\t\t\t\tgroupsJS();
\t\t\t} else {
\t\t\t\t\$(\"#createresponse\").innerHTML = r.msg;
\t\t\t}
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/groups.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
