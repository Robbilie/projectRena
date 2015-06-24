<?php

/* /pages/settings.twig */
class __TwigTemplate_042be5b1d72171f38fd06220d9b12ff7f20610507ae9e9601384733f561a8690 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"settingsConti\">
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h2 class=\"blue\">Add API Key</h2>
\t\t</div>
\t\t<div class=\"cbody paddedp\">
\t\t\t<h4 id=\"submitresponse\"></h4>
\t\t\t<!--<div class=\"divider\"></div>-->
\t\t\t<h4 class=\"mbn\">Key ID</h4>
\t\t\t<input type=\"text\" name=\"keyID\" id=\"keyID\"/>
\t\t\t<h4 class=\"mbn\">vCode</h4>
\t\t\t<input type=\"text\" name=\"vCode\" id=\"vCode\"/>
\t\t\t<div class=\"divider\"></div>
\t\t\t<span class=\"btn\" onclick=\"submitAPI();\">Submit</span>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tsettingsJS();
\tfunction settingsJS () {
\t\tfadeOn(\$(\"#settingsConti\"), 1);
\t}
\tfunction submitAPI () {
\t\tvar keyID = document.getElementsByName(\"keyID\")[0].value;
\t\tvar vCode = document.getElementsByName(\"vCode\")[0].value;
\t\tif(keyID == \"\" || vCode == \"\") return;
\t\tajax(\"/json/apikey/\" + keyID + \"/\" + vCode + \"/\", function (r) {
\t\t\t\$(\"#keyID\").value = \"\";
\t\t\t\$(\"#vCode\").value = \"\";
\t\t\t\$(\"#submitresponse\").innerHTML = r.msg;
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/settings.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
