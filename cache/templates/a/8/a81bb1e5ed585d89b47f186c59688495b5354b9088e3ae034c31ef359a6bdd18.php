<?php

/* /pages/charactersheet.twig */
class __TwigTemplate_a81bb1e5ed585d89b47f186c59688495b5354b9088e3ae034c31ef359a6bdd18 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"charactersheetConti\">
\t<h1 class=\"blue tu ml\">Active Character</h1>
\t<p class=\"inset inline-block\">
\t\t<img class=\"block\" id=\"characterImg\"><img id=\"corporationImg\"><img id=\"allianceImg\">
\t</p>
\t<div class=\"inline-block ml\">
\t\t<h4 class=\"mbn\">Character Name</h4>
\t\t<h2 class=\"mtn dark\" id=\"characterName\"></h2>
\t\t<h4 class=\"mbn\">Corporation Name</h4>
\t\t<h2 class=\"mtn dark\" id=\"corporationName\"></h2>
\t\t<h4 class=\"mbn\">Alliance Name</h4>
\t\t<h2 class=\"mtn dark\" id=\"allianceName\"></h2>
\t</div>
\t<div class=\"inline-block ml\">
\t\t<h4 class=\"mbn\">Groups</h4>
\t\t<div id=\"groupList\"></div>
\t</div>
\t<div class=\"divider\"></div>
\t<h1 class=\"blue tu ml\">All Characters</h1>
\t<div class=\"ml mr\" id=\"characterList\"></div>
\t<div class=\"divider\"></div>
\t<h1 class=\"blue tu ml\">Add Character</h1>
\t<a class=\"ml\" id=\"charSwitch\" href=\"";
        // line 23
        echo twig_escape_filter($this->env, (isset($context["EVESSOURL"]) ? $context["EVESSOURL"] : null), "html", null, true);
        echo "\"><img src=\"/img/EVE_SSO_Login_Buttons_Small_White.png\" alt=\"EVE SSO Login Button\"></a>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tprofileJS();
\tfunction profileJS () {
\t\t\$(\"#charSwitch\").href = loginurl + escape(\"#!/profile/\");
\t\tajax(\"/json/character/\" + coreStatus.charid + \"/\", function (r) {
\t\t\t\$(\"#characterImg\").src = \"https://image.eveonline.com/Character/\" + r.characterID + \"_128.jpg\";
\t\t\t\$(\"#characterImg\").alt = r.characterName;
\t\t\t\$(\"#corporationImg\").src = \"https://image.eveonline.com/Corporation/\" + r.corporationID + \"_64.png\";
\t\t\t\$(\"#corporationImg\").alt = r.corporationName;
\t\t\t\$(\"#allianceImg\").src = \"https://image.eveonline.com/Alliance/\" + r.allianceID + \"_64.png\";
\t\t\t\$(\"#allianceImg\").alt = r.allianceName;

\t\t\t\$(\"#characterName\").innerHTML = r.characterName;
\t\t\t\$(\"#corporationName\").innerHTML = r.corporationName;
\t\t\t\$(\"#allianceName\").innerHTML = r.allianceName;

\t\t\tfadeOn(\$(\"#charactersheetConti\"), 1);
\t\t}, \"json\");
\t\tajax(\"/json/characters/\", function (r) {
\t\t\tvar el = \$(\"#characterList\");
\t\t\tel.innerHTML = '';
\t\t\tfor (var i = r.length - 1; i >= 0; i--) {
\t\t\t\tel.innerHTML += '<div class=\"hover row\">' + 
\t\t\t\t\t'<img src=\"https://image.eveonline.com/Alliance/' + r[i]['allianceID'] + '_32.png\" alt=\"' + r[i]['allianceName'] + '\"/>' + 
\t\t\t\t\t'<img src=\"https://image.eveonline.com/Corporation/' + r[i]['corporationID'] + '_32.png\" alt=\"' + r[i]['corporationName'] + '\"/>' + 
\t\t\t\t\t'<img src=\"https://image.eveonline.com/Character/' + r[i]['characterID'] + '_32.jpg\" alt=\"' + r[i]['characterName'] + '\"/>' + 
\t\t\t\t\t'<span onclick=\"switchCharacter(' + r[i]['characterID'] + ');\">' + r[i]['characterName'] + '</span>' + 
\t\t\t\t\t(r[i]['characterID'] != coreStatus.charid ? '<span class=\"fr hover\" onclick=\"deleteCharacter(' + r[i]['characterID'] + ');\">&times;</span>' : '') + 
\t\t\t\t'</div>';
\t\t\t};
\t\t}, \"json\");
\t\tajax(\"/json/character/\" + coreStatus.charid + \"/groups/\", function (r) {
\t\t\tvar el = \$(\"#groupList\");
\t\t\tel.innerHTML = '';
\t\t\tfor (var i = r.length - 1; i >= 0; i--) {
\t\t\t\tel.innerHTML += '<div> + ' + r[i]['name'] + '</div>';
\t\t\t};
\t\t}, \"json\");
\t}

\tfunction deleteCharacter (charid) {
\t\tajax(\"/json/character/delete/\" + charid + \"/\", function (r) {
\t\t\tif(r.state == \"success\")
\t\t\t\thashChange();
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/charactersheet.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  43 => 23,  19 => 1,);
    }
}
