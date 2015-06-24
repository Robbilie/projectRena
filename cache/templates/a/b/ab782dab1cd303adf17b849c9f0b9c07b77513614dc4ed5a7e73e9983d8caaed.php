<?php

/* navbar.twig */
class __TwigTemplate_ab782dab1cd303adf17b849c9f0b9c07b77513614dc4ed5a7e73e9983d8caaed extends Twig_Template
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
        echo "<div class=\"navbar navbar-default navbar-inverse navbar-fixed-top\" role=\"navigation\">
    <div class=\"container\">
        <div class=\"navbar-header\">
            <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
                <span class=\"icon-bar\"></span>
            </button>
            <a href=\"#\" class=\"navbar-brand\">Project Rena</a>
        </div>
        <div class=\"collapse navbar-collapse\">
            <ul class=\"nav navbar-nav navbar-right\">
                <li><a href=\"#\">Home</a></li>
                <li><a href=\"#\">Services</a></li>

                <li class=\"dropdown\">
                    <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">
                        Tools
                        <span class=\"caret\"></span>
                    </a>
                    <ul class=\"dropdown-menu\">
                        <li><a href=\"/paste/\">Paste</a></li>
                        <li><a href=\"#\">Link 2</a></li>
                        <li><a href=\"#\">Link 3</a></li>
                    </ul>
                </li>

                ";
        // line 28
        if ((isset($context["LoggedIN"]) ? $context["LoggedIN"] : null)) {
            // line 29
            echo "                    <li class=\"dropdown\">
                        <a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">
                            <strong>";
            // line 31
            echo twig_escape_filter($this->env, (isset($context["characterName"]) ? $context["characterName"] : null), "html", null, true);
            echo "</strong>
                            <img src=\"";
            // line 32
            echo twig_escape_filter($this->env, (isset($context["imageServer"]) ? $context["imageServer"] : null), "html", null, true);
            echo "/Character/";
            echo twig_escape_filter($this->env, (isset($context["characterID"]) ? $context["characterID"] : null), "html", null, true);
            echo "_32.jpg\" class=\"img-circle\"
                                 width=\"18px\">
                            <span class=\"caret\"></span>
                        </a>
                        <ul class=\"dropdown-menu\">
                            <li>
                                <div class=\"navbar-login\">
                                    <div class=\"row\">
                                        <div class=\"col-lg-4\">
                                            <p class=\"text-center\">
                                                <img src=\"";
            // line 42
            echo twig_escape_filter($this->env, (isset($context["imageServer"]) ? $context["imageServer"] : null), "html", null, true);
            echo "/Character/";
            echo twig_escape_filter($this->env, (isset($context["characterID"]) ? $context["characterID"] : null), "html", null, true);
            echo "_128.jpg\"
                                                     class=\"img-circle\" width=\"100px\">
                                            </p>
                                        </div>
                                        <div class=\"col-lg-8\">
                                            <p class=\"text-left\"><strong>";
            // line 47
            echo twig_escape_filter($this->env, (isset($context["characterName"]) ? $context["characterName"] : null), "html", null, true);
            echo "</strong></p>

                                            <p class=\"text-left small\">Some Corp | Triumvirate.</p>

                                            <p class=\"text-left\">
                                                <a href=\"#\" class=\"btn btn-primary btn-block btn-sm\">Settings</a>
                                                <a href=\"#\" class=\"btn btn-primary btn-block btn-sm\">API Settings</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class=\"divider\"></li>
                            <li>
                                <div class=\"navbar-login navbar-login-session\">
                                    <div class=\"row\">
                                        <div class=\"col-lg-12\">
                                            <p>
                                                <a href=\"/logout/\" class=\"btn btn-danger btn-block\">Logout</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </li>
                ";
        } else {
            // line 74
            echo "                    <li><a href=\"";
            echo twig_escape_filter($this->env, (isset($context["EVESSOURL"]) ? $context["EVESSOURL"] : null), "html", null, true);
            echo "\"><img
                                    src=\"https://images.contentful.com/idjq7aai9ylm/18BxKSXCymyqY4QKo8KwKe/c2bdded6118472dd587c8107f24104d7/EVE_SSO_Login_Buttons_Small_White.png?w=195&h=18\"></a>
                    </li>
                ";
        }
        // line 78
        echo "            </ul>
        </div>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "navbar.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  121 => 78,  113 => 74,  83 => 47,  73 => 42,  58 => 32,  54 => 31,  50 => 29,  48 => 28,  19 => 1,);
    }
}
