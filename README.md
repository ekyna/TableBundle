TableBundle
===========

Table component integration.

## Installation

Install this package through composer:

```
composer require ekyna/table-bundle:0.7.x-dev
```

Register the bundle in your AppKernel: 

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

## Example

Given there is a __Brand__ doctrine entity configured in the AcmeDemoBundle with a title field. 

Create the table type:
 
```php
// src/Acme/DemoBundle/Table/Type/BrandType.php
namespace Acme\DemoBundle\Table\Type;

use Acme\DemoBundle\Entity\Brand;
use Ekyna\Component\Table\AbstractTableType;
use Ekyna\Component\Table\Extension\Core\Type\Column;
use Ekyna\Component\Table\Extension\Core\Type\Filter;
use Ekyna\Component\Table\TableBuilderInterface;        
use Ekyna\Component\Table\Bridge\Doctrine\ORM\Source\EntitySource;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BrandType extends AbstractTableType
{
    public function buildTable(TableBuilderInterface $tableBuilder)
    {
        $tableBuilder
            ->addColumn('id', Column\NumberType::class)
            ->addColumn('title', Column\TextType::class, [
                'label' => 'Title',
            ])
            ->addFilter('id', Filter\NumberType::class)
            ->addFilter('title', Filter\TitleType::class, [
                'label' => 'Titre'
            ])
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'source' => new EntitySource(Brand::class)
        ));
    }
}
```

(optional) Register the table type as a service:

```xml
<!-- src/Acme/DemoBundle/Resources/config/services.xml -->
<service id="acme_demo.table.brand_type" class="Acme\DemoBundle\Table\Type\BrandType">
    <tag name="table.type" />
</service>
```

Usage in a controller:

```php
// src/Acme/DemoBundle/Controller/BrandController.php
namespace Acme\Demo\Controller;

use Acme\DemoBundle\Table\Type\BrandType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ResourceController extends Controller
{
    public function indexAction(Request $request)
    {
        $table = $this
            ->get('table.factory')
            ->createTable('brands', BrandType::class);
         
        if (null !== $response = $table->handleRequest($request)) {
            return $response;
        }
        
        return $this->render('AcmeDemoBundle:Brand:index.html.twig', array(
            'brands' => $table->createView(),
        ));
    }
}
```

Usage in a twig template:

```twig
# src/Acme/DemoBundle/Resources/views/Brand/index.html.twig
<!DOCTYPE html>
<html>
    <head>
        {% block stylesheets %}
        <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="{{ asset('bundles/ekynatable/css/table.css') }}" rel="stylesheet" type="text/css"/>
        {% endblock stylesheets %}        
    </head>
    <body>
        {{ ekyna_table_render(brands) }}
        
        {% block javascripts %}
        <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <script src="{{ asset('bundles/ekynatable/js/table.js') }}"></script>
        {% endjavascripts %}
    </body>
</html>
```

## Customization

The default template used to render the table is `vendor/ekyna/table-bundle/Ekyna/Bundle/TableBundle/Resources/views/table.html.twig`.
It requires jQuery and Bootstrap 3.

You can create your own rendering template (where you will define all blocks of the default template) and use it this way

```twig
    {{ ekyna_table_render(brands, {'template': 'AcmeDemoBundle:Table:render.html.twig'}) }}
```

## What's next ?

- Templating engine with type/template hierarchy.
- Better export implementation.
- Tests.
- Demo repository.
- More doc.
- More sources.
- AJAX support.

 
