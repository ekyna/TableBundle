TableBundle
===========

Table component integration.

## Instation

 1. Through Composer

```
    composer require ekyna/table-bundle:0.1.*@dev
```

 2. Register the bundle in your AppKernel 

```php
    // app/AppKernel.php
    public function registerBundles()
    {
        $bundles = array(
            // other bundles ...
            new Ekyna\Bundle\TableBundle\EkynaTableBundle(),
        );
     
        return $bundles;
    }
```

## Usage

1. Create the table type
 
```php
    // src/Acme/DemoBundle/Table/Type/BrandType.php
    namespace Acme\DemoBundle\Table\Type;
    
    use Ekyna\Component\Table\AbstractTableType;
    use Ekyna\Component\Table\TableBuilderInterface;
    use Symfony\Component\OptionsResolver\OptionsResolverInterface;
    
    class BrandType extends AbstractTableType
    {
        public function buildTable(TableBuilderInterface $tableBuilder)
        {
            $tableBuilder
                ->addColumn('id', 'number', array(
                    'sortable' => true,
                ))
                ->addColumn('title', 'text', array(
                    'label' => 'Title',
                    'sortable' => true,
                ))
                ->addFilter('id', 'number')
                ->addFilter('title', 'text', array(
                    'label' => 'Title'
                ))
            ;
        }
        
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            parent::setDefaultOptions($resolver);
        
            $resolver->setDefaults(array(
                'data_class' => 'Acme\DemoBundle\Entity\Brand',
            ));
        }
        
        public function getName()
        {
            return 'acme_demo_brand';
        }
    }
```

2. (optional) Register the table type as a service

```xml
    <!-- src/Acme/DemoBundle/Resources/config/services.xml -->
    <service id="acme_demo.table_type.brand" class="Acme\DemoBundle\Table\Type\BrandType">
        <tag name="table.type" alias="acme_demo_brand" />
    </service>
```

3. Create the controller

```php
    // src/Acme/DemoBundle/Controller/BrandController.php
    namespace Acme\Demo\Controller;
    
    use Symfony\Bundle\FrameworkBundle\Controller\Controller;
    use Symfony\Component\HttpFoundation\Request;
    // use Acme\DemoBundle\Table\Type\BrandType;
    
    class ResourceController extends Controller
    {
        public function indexAction(Request $request)
        {
            $table = $this->get('table.factory')
                /*->createBuilder(new BrandType(), array( // instance
                    'name' => 'my_brand_list,
                ))*/
                ->createBuilder('acme_demo_brand', array( // service
                    'name' => 'my_brand_list',
                ))
                ->getTable($request)
            ;
            
            return $this->render('AcmeDemoBundle:Brand:index.html.twig', array(
                'brands' => $table->createView(),
            ));
        }
    }
```

4. Create the twig template

```twig
    # src/Acme/DemoBundle/Resources/views/Brand/index.html.twig
    <!DOCTYPE html>
    <html>
        <head>
            {% stylesheets output='css/main.css'
                'css/bootstrap.css'
                '@EkynaTableBundle/Resources/asset/css/table.css'
            -%}
            <link href="{{ asset_url }}" rel="stylesheet" type="text/css" />
            {% endstylesheets %}
        </head>
        <body>
            {{ ekyna_table_render(brands) }}
            
            {% javascripts output='js/main.js'
                'js/jquery.js'
                'js/bootstrap.js'
                '@EkynaTableBundle/Resources/asset/js/table.js'
            -%}
            <script type="text/javascript" src="{{ asset_url }}"></script>
            {%- endjavascripts %}
        </body>
    </html>
```

## Customization

The default template used to render the table is `vendor/ekyna/table-bundle/Ekyna/Bundle/TableBundle/Resources/views/ekyna_table.html.twig`.
It requires jQuery and Bootstrap 3.

You can create your own rendering template (where you will define all blocks of the default template) and use it this way

```twig
    {{ ekyna_table_render(brands, {'template': 'AcmeDemoBundle:Table:render.html.twig'}) }}
```

## TODO
 * Tests
 * Type inheritance
 * AJAX
 * Adapters (ORM, ODM, PHPCR)
 * Render engines
 * More documentation (columns, filter, internals, ...)
 
