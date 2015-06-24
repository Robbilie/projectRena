<?php

/* /pages/contents.twig */
class __TwigTemplate_9b20a89dc22b8f5d3c13a67181317c4a9714d8d5bf245be3426b29d0de4e88fc extends Twig_Template
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
        echo "<div class=\"contentConti\" id=\"contentsConti\">
\t<div class=\"card\">
\t\t<div class=\"chead\">
\t\t\t<h3 class=\"blue\" id=\"contentsname\"></h3>
\t\t</div>
\t\t<div class=\"cbody\" id=\"contentlist\"></div>
\t</div>
\t<div style=\"display: none;\">
\t\t<div id=\"contentsTemplate\">
\t\t\t<div class=\"hover paddedp\"><a {0} onclick=\"click(this);\">{1}({2})</a></div>
\t\t</div>
\t</div>
</div>
<label id=\"maxBtn\" for=\"checkMaxed\">+</label>
<script type=\"text/javascript\">
\tcontentsJS();
\tfunction contentsJS () {
\t\tajax(\"/json\" + location.hash.slice(2), function (r) {
\t\t\tconsole.log(r);
\t\t\t\$(\"#contentsname\").innerHTML = r.name;
\t\t\tvar tmpl = \$(\"#contentsTemplate\").innerHTML;
\t\t\tvar el = \$(\"#contentlist\");
\t\t\tel.innerHTML = \"\";
\t\t\tfor(var i = 0; i < r.list.length; i++)
\t\t\t\tel.innerHTML += tmpl.format(r.list[i]['flag'] == 0 ? 'href=\"#!/corporation/' + r.list[i]['ownerID'] + '/container/' + r.list[i]['itemID'] + '/\"' : '', r.list[i]['name'] ? r.list[i]['name'] : \"\", r.list[i]['typeName']);

\t\t\tfadeOn(\$(\"#contentsConti\"), 1);
\t\t}, \"json\");
\t}
</script>";
    }

    public function getTemplateName()
    {
        return "/pages/contents.twig";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }
}
