<?php

/* base.twig */
class __TwigTemplate_5de07057f764396e0c7fd8c5738291ac44a0561b5a8471755960fc0c4803b5f4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'css' => array($this, 'block_css'),
            'content' => array($this, 'block_content'),
            'js' => array($this, 'block_js'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!doctype html>
<html>
<head>
    <title>EVSCO ";
        // line 4
        if ((isset($context["pageTitle"]) ? $context["pageTitle"] : null)) {
            echo "/ ";
            echo twig_escape_filter($this->env, (isset($context["pageTitle"]) ? $context["pageTitle"] : null), "html", null, true);
        }
        echo "</title>
    <meta name=\"viewport\" content=\"width=device-width\">
    <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css\">
    <link rel=\"stylesheet\" href=\"//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/css/bootstrap.min.css\">
    <link rel=\"stylesheet\" href=\"/css/main.css\">
    <style type=\"text/css\">
        body {
            background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABoAAAAaCAYAAACpSkzOAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMC8yOS8xMiKqq3kAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzVxteM2AAABHklEQVRIib2Vyw6EIAxFW5idr///Qx9sfG3pLEyJ3tAwi5EmBqRo7vHawiEEERHS6x7MTMxMVv6+z3tPMUYSkfTM/R0fEaG2bbMv+Gc4nZzn+dN4HAcREa3r+hi3bcuu68jLskhVIlW073tWaYlQ9+F9IpqmSfq+fwskhdO/AwmUTJXrOuaRQNeRkOd5lq7rXmS5InmERKoER/QMvUAPlZDHcZRhGN4CSeGY+aHMqgcks5RrHv/eeh455x5KrMq2yHQdibDO6ncG/KZWL7M8xDyS1/MIO0NJqdULLS81X6/X6aR0nqBSJcPeZnlZrzN477NKURn2Nus8sjzmEII0TfMiyxUuxphVWjpJkbx0btUnshRihVv70Bv8ItXq6Asoi/ZiCbU6YgAAAABJRU5ErkJggg==);
        }

        .modal-body {
            overflow-y: visible;
        }

        #login-nav input {
            margin-bottom: 15px;
        }
    </style>
    ";
        // line 22
        $this->displayBlock('css', $context, $blocks);
        // line 24
        echo "</head>

<body>
";
        // line 27
        $this->loadTemplate("navbar.twig", "base.twig", 27)->display($context);
        // line 28
        echo "
<div class=\"container\" name=\"content\">
    ";
        // line 30
        $this->displayBlock('content', $context, $blocks);
        // line 32
        echo "</div>

<div class=\"container\" name=\"footer\">
    <hr>
    <footer>
        <p>&copy; EVSCO 2014-2015</p>
    </footer>
</div>

<script type=\"text/javascript\" src=\"//cdnjs.cloudflare.com/ajax/libs/jquery/1.11.0/jquery.min.js\"></script>
<script type=\"text/javascript\"
        src=\"//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.4/js/bootstrap.min.js\"></script>
";
        // line 44
        $this->displayBlock('js', $context, $blocks);
        // line 45
        echo "</body>
</html>";
    }

    // line 22
    public function block_css($context, array $blocks = array())
    {
        // line 23
        echo "    ";
    }

    // line 30
    public function block_content($context, array $blocks = array())
    {
        // line 31
        echo "    ";
    }

    // line 44
    public function block_js($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "base.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 44,  97 => 31,  94 => 30,  90 => 23,  87 => 22,  82 => 45,  80 => 44,  66 => 32,  64 => 30,  60 => 28,  58 => 27,  53 => 24,  51 => 22,  27 => 4,  22 => 1,);
    }
}
