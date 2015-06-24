<?php

/* /pages/controltower.twig */
class __TwigTemplate_acebe8486200149d5fcab550bcd5dc99fc5d9dfdb1bfd551a1e4e7be46537814 extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"controltowerConti\">
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h1 class=\"blue\" id=\"controltowername\"></h1>
\t\t</div>
\t\t<div class=\"cbody\">
\t\t\t<div class=\"paddedp\">
\t\t\t\t<table border=\"0\" class=\"half\">
\t\t\t\t\t<tr><th></th><th></th></tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td><span class=\"info hover\" id=\"controltowernotif\"><div class=\"notif\"></div>i</span>Notifications</td>
\t\t\t\t\t\t<td id=\"controltowermoonname\"></td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr>
\t\t\t\t\t\t<td id=\"controltowerstatename\"></td>
\t\t\t\t\t\t<td id=\"controltowertypename\"></td>
\t\t\t\t\t</tr>
\t\t\t\t\t<tr><td>Fuel</td></tr>
\t\t\t\t\t<tr><td colspan=\"3\"><div class=\"progressBar\"><div id=\"controltowerfuel\"></div></div></td></tr>
\t\t\t\t\t<tr><td>Strontium</td></tr>
\t\t\t\t\t<tr><td colspan=\"3\"><div class=\"progressBar\"><div id=\"controltowerstrontium\"></div></div></td></tr>
\t\t\t\t</table>
\t\t\t</div>
\t\t</div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h3 class=\"blue\">Modules</h3>
\t\t</div>
\t\t<div class=\"cbody\" id=\"moduleslist\"></div>
\t</div>
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h3 class=\"blue\">Reactions</h3>
\t\t</div>
\t\t<div class=\"cbody\">
\t\t</div>
\t</div>
\t<div style=\"display: none;\">
\t\t<div id=\"modulesTemplate\">
\t\t\t<div class=\"hover paddedp\"><a href=\"#!/corporation/{0}/container/{1}/\" onclick=\"click(this);\">{2}</a></div>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tstructurescontroltowerJS();
\tfunction structurescontroltowerJS () {
\t\tajax(\"/json\" + location.hash.slice(2), function (r) {
\t\t\tvar states = [\"Unanchored\", \"Anchored / Offline\", \"Onlining\", \"Reinforced\", \"Online\"];

\t\t\t\$(\"#controltowernotif\").onmouseover = function () { loadNotif(r.id, this); };

\t\t\t\$(\"#controltowername\").innerHTML = r.name;
\t\t\t\$(\"#controltowermoonname\").innerHTML = r.moonname;
\t\t\t\$(\"#controltowerstatename\").innerHTML = states[r.state];
\t\t\t\$(\"#controltowertypename\").innerHTML = r.typename;
\t\t\t\$(\"#controltowerfuel\").style.width = r.fuel + \"%\";
\t\t\t\$(\"#controltowerstrontium\").style.width = r.strontium + \"%\";

\t\t\tvar tmpl = \$(\"#modulesTemplate\").innerHTML;
\t\t\tvar el = \$(\"#moduleslist\");
\t\t\tel.innerHTML = \"\";
\t\t\tfor(var i = 0; i < r.modules.length; i++)
\t\t\t\tel.innerHTML += tmpl.format(r.modules[i]['ownerID'], r.modules[i]['itemID'], r.modules[i]['name']);

\t\t\tfadeOn(\$(\"#controltowerConti\"), 1);
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/controltower.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
