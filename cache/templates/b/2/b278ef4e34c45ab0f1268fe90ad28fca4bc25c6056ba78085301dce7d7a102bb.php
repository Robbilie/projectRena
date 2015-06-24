<?php

/* /pages/profile.twig */
class __TwigTemplate_b278ef4e34c45ab0f1268fe90ad28fca4bc25c6056ba78085301dce7d7a102bb extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"profileConti\">
\t<h1 class=\"blue tu ml\">Character</h1>
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
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tprofileJS();
\tfunction profileJS () {
\t\tajax(\"/json/character/\" + location.hash.split(\"/\")[2] + \"/\", function (r) {
\t\t\t\$(\"#characterImg\").src = \"https://image.eveonline.com/Character/\" + r.characterID + \"_128.jpg\";
\t\t\t\$(\"#characterImg\").alt = r.characterName;
\t\t\t\$(\"#corporationImg\").src = \"https://image.eveonline.com/Corporation/\" + r.corporationID + \"_64.png\";
\t\t\t\$(\"#corporationImg\").alt = r.corporationName;
\t\t\t\$(\"#allianceImg\").src = \"https://image.eveonline.com/Alliance/\" + r.allianceID + \"_64.png\";
\t\t\t\$(\"#allianceImg\").alt = r.allianceName;

\t\t\t\$(\"#characterName\").innerHTML = r.characterName;
\t\t\t\$(\"#corporationName\").innerHTML = r.corporationName;
\t\t\t\$(\"#allianceName\").innerHTML = r.allianceName;

\t\t\tfadeOn(\$(\"#profileConti\"), 1);
\t\t}, \"json\");
\t\tajax(\"/json/character/\" + coreStatus.charid + \"/groups/\", function (r) {
\t\t\tvar el = \$(\"#groupList\");
\t\t\tel.innerHTML = '';
\t\t\tfor (var i = r.length - 1; i >= 0; i--) {
\t\t\t\tel.innerHTML += '<div> + ' + r[i]['name'] + '</div>';
\t\t\t};
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/profile.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
