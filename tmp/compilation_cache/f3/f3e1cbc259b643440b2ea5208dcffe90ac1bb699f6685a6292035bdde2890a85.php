<?php

/* categories/index.php */
class __TwigTemplate_cf5d45a67e469cb6e0eaf45c0ad133c0c8398bd6949598b01af6b38f80c1ea76 extends Twig_Template
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
        echo "<div><h2>Please select a category</h2>
<?php foreach (\$categories as \$category):?>
<div class=\"category\">

<?php echo \$html->link(\$category['name'],'categories/view/'.\$category['id'].'/'.\$category['name'])?>

</div>
<?php endforeach?>
</div>";
    }

    public function getTemplateName()
    {
        return "categories/index.php";
    }

    public function getDebugInfo()
    {
        return array (  19 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "categories/index.php", "/var/www/tinyfam.floretos.com/application/views/categories/index.php");
    }
}
