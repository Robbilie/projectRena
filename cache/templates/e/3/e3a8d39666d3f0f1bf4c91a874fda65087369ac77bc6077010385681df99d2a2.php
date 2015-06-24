<?php

/* /pages/assets.twig */
class __TwigTemplate_e3a8d39666d3f0f1bf4c91a874fda65087369ac77bc6077010385681df99d2a2 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"assetsConti\">
\t<div id=\"assetsNav\" class=\"navigation\">
\t\t<div id=\"personalNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/assets/personal/\">Personal</a></div><div id=\"corporationNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/assets/corporation/\">Corporation</a></div><div id=\"allianceNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/assets/alliance/\">Alliance</a></div><div id=\"memberNav\" class=\"quat\"><a onclick=\"click(this);\" href=\"#!/assets/member/\">Member</a></div>
\t</div>
\t<div id=\"assetsContent\"></div>
\t<div style=\"display: none;\">
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tassetsJS();
\tfunction assetsJS () {
\t\tif(location.hash.split(\"/\")[2] == \"\") {
\t\t\tvar t = \$(\"#personalNav\").children[0];
\t\t\tlocation.hash = t.getAttribute(\"href\");
\t\t\tclick(t);
\t\t} else {
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className.replace(\" selected\", \"\");
\t\t\t\$(\"#\" + location.hash.split(\"/\")[2] + \"Nav\").className += \" selected\";
\t\t\tvar fn = \"assets\" + location.hash.split(\"/\")[2] + \"JS\";
\t\t\tif(typeof window[fn] === \"function\")
\t\t\t\twindow[fn](function () {
\t\t\t\t\tfadeOn(\$(\"#assetsConti\"), 1);
\t\t\t\t});
\t\t}
\t}

\tfunction assetspersonalJS (cb) {
\t\tcb();
\t}

\tfunction assetscorporationJS (cb) {
\t\tcb();
\t}

\tfunction assetsallianceJS (cb) {
\t\tcb();
\t}

\tfunction assetsmemberJS (cb) {
\t\tcb();
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/assets.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
