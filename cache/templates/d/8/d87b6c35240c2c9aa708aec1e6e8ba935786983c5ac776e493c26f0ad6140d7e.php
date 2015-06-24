<?php

/* /pages/group.twig */
class __TwigTemplate_d87b6c35240c2c9aa708aec1e6e8ba935786983c5ac776e493c26f0ad6140d7e extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"groupConti\">
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h1 class=\"blue\" id=\"groupName\"></h1>
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Permissions</h2>
\t\t</div>
\t\t<div id=\"permissionList\" class=\"cbody\">
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Members</h2>
\t\t</div>
\t\t<div id=\"memberList\" class=\"cbody\">
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Add Character</h2>
\t\t</div>
\t\t<div class=\"cbody paddedp\">
\t\t\t<h4 id=\"addresponse\"></h4>
\t\t\t<h4 class=\"mbn\">Character Name</h4>
\t\t\t<input type=\"text\" id=\"charId\" data-url=\"/json/characternames/:param\"/>
\t\t\t<div class=\"divider\"></div>
\t\t\t<span class=\"btn\" onclick=\"addCharacter();\">Add</span>
\t\t</div>
\t</div>
\t<div style=\"display: none;\">
\t\t<div id=\"permissionTemplate\">
\t\t\t<div class=\"hover row\"><input onchange=\"changePermission(this);\" data-permissionid=\"{2}\" data-groupid=\"{3}\" type=\"checkbox\" id=\"{1}Perm\" {0} ><label for=\"{1}Perm\">{1}</label></div>
\t\t</div>
\t\t<div id=\"memberTemplate\">
\t\t\t<div class=\"hover row\"><a href=\"#!/profile/{0}/\" onclick=\"click(this);\">{1}</a>{2}</div>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tvar groupScope = \"default\";
\tvar groupCustom = 0;
\tvar groupPermissions = 0;
    var namedd;
    var charToAdd = null;
\tgroupJS();
\tfunction groupJS () {
\t\tajax(\"/json/group/\" + location.hash.split(\"/\")[2] + \"/\", function (r) {
\t\t\tconsole.log(r);
\t\t\t\$(\"#groupName\").innerHTML = r.name + '<span class=\"fr\">[' + (r.custom == 1 ? 'custom ' : '') + r.scope + ']</span>';
\t\t\tgroupScope = r.scope;
\t\t\tgroupCustom = r.custom;
\t\t\tgroupPermissions = r.permissions;

\t\t\tloadPermissions();

\t\t\tloadMembers();

            namedd = new AutoComplete(\"charId\");
            namedd.oncomplete = function (self, el) {
                charToAdd = el.getAttribute(\"data-dat\");
            };

\t\t\tfadeOn(\$(\"#groupConti\"), 1);
\t\t}, \"json\");
\t}

\tfunction loadPermissions () {
\t\tajax(\"/json/permissions/\" + groupScope + \"/\", function (s) {
\t\t\tconsole.log(s);
\t\t\tvar tmpl = \$(\"#permissionTemplate\").innerHTML;
\t\t\tvar el = \$(\"#permissionList\");
\t\t\tel.innerHTML = \"\";
\t\t\tfor(var i = 0; i < s.length; i++)
\t\t\t\tel.innerHTML += tmpl.format((groupPermissions.indexOf(parseInt(s[i].id)) >= 0) ? 'checked=\"true\"' : '', s[i].name, s[i].id, location.hash.split(\"/\")[2]);
\t\t}, \"json\");
\t}

\tfunction loadMembers () {
\t\tajax(\"/json/group/\" + location.hash.split(\"/\")[2] + \"/members/\", function (t) {
\t\t\tvar tmpl = \$(\"#memberTemplate\").innerHTML;
\t\t\tvar el = \$(\"#memberList\");
\t\t\tel.innerHTML = \"\";
\t\t\tfor(var i = 0; i < t.length; i++)
\t\t\t\tel.innerHTML += tmpl.format(t[i].characterID, t[i].characterName, groupCustom == 1 ? '<span class=\"fr hover\" onclick=\"removeCharacter(' + t[i]['characterID'] + ');\">&times;</span>' : '');
\t\t}, \"json\");
\t}

\tfunction changePermission (el) {
\t\tajax(\"/json/group/\" + location.hash.split(\"/\")[2] + \"/\" + (el.checked ? \"add\" : \"remove\") + \"/permission/\" + el.getAttribute(\"data-permissionid\") + \"/\", function (r) {
\t\t\tconsole.log(r);
\t\t\tif(r.state == \"error\")
\t\t\t\tel.checked = !el.checked;
\t\t}, \"json\");
\t}

\tfunction removeCharacter (charid) {
\t\tajax(\"/json/group/\" + location.hash.split(\"/\")[2] + \"/remove/character/\" + charid + \"/\", function (r) {
\t\t\tif(r.state == \"success\")
\t\t\t\tloadMembers();
\t\t}, \"json\");
\t}

\tfunction addCharacter () {
\t\tajax(\"/json/group/\" + location.hash.split(\"/\")[2] + \"/add/character/\" + charToAdd + \"/\", function (r) {
\t\t\tif(r.state == \"success\") {
\t\t\t\tloadMembers();
\t\t\t} else {
\t\t\t\t\$(\"#addresponse\").innerHTML = \"error\";
\t\t\t}
\t\t}, \"json\");
\t}

</script>";
    }

    public function getTemplateName()
    {
        return "/pages/group.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
