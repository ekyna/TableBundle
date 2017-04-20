<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Ekyna\Bundle\TableBundle\Extension\Type\Extension\BooleanColumnTypeExtension;
use Ekyna\Bundle\TableBundle\Extension\Type\Extension\BooleanFilterTypeExtension;
use Ekyna\Bundle\TableBundle\Extension\Type\Extension\BootstrapTypeExtension;
use Ekyna\Bundle\TableBundle\Extension\Type\Extension\TranslateActionTypeExtension;
use Ekyna\Bundle\TableBundle\Extension\Type\Extension\TranslateColumnTypeExtension;
use Ekyna\Bundle\TableBundle\Extension\Type\Extension\TranslateFilterTypeExtension;
use Ekyna\Component\Table\Bridge\Doctrine\ORM\DoctrineORMExtension;
use Ekyna\Component\Table\Bridge\Symfony;
use Ekyna\Component\Table\Bridge\Twig\TableExtension;
use Ekyna\Component\Table\Bridge\Twig\TableRenderer;
use Ekyna\Component\Table\TableFactoryInterface;
use Ekyna\Component\Table\RegistryInterface;
use Ekyna\Component\Table\ResolvedTypeFactory;
use Ekyna\Component\Table\TableTableFactory;
use Ekyna\Component\Table\TableRegistry;

return static function (ContainerConfigurator $container) {
    $container
        ->services()

        // Boolean column type extension
        ->set('table.column_type_extension.boolean', BooleanColumnTypeExtension::class)
            ->args([
                service('translator'),
            ])
            ->tag('table.column_type_extension')

        // Boolean filter type extension
        ->set('table.filter_type_extension.boolean', BooleanFilterTypeExtension::class)
            ->args([
                service('translator'),
            ])
            ->tag('table.filter_type_extension')

        // Bootstrap CSS classes table extension
        ->set('table.type_extension.bootstrap', BootstrapTypeExtension::class)
            ->tag('table.type_extension')

        // Translate action type extension
        ->set('table.action_type_extension.translate', TranslateActionTypeExtension::class)
            ->args([
                service('translator'),
            ])
            ->tag('table.action_type_extension')

        // Translate column type extension
        ->set('table.column_type_extension.translate', TranslateColumnTypeExtension::class)
            ->args([
                service('translator'),
            ])
            ->tag('table.column_type_extension')

        // Translate filter type extension
        ->set('table.filter_type_extension.translate', TranslateFilterTypeExtension::class)
            ->args([
                service('translator'),
            ])
            ->tag('table.filter_type_extension')

        // Http foundation table type extension
        ->set('table.type_extension.http_foundation', Symfony\HttpFoundation\TableTypeExtension::class)
            ->args([
                service('request_stack'),
                service('translator'),
            ])
            ->tag('table.type_extension')

        // Session table type extension
        ->set('table.type_extension.session', Symfony\Session\TableTypeExtension::class)
            ->args([
                service('request_stack'),
            ])
            ->tag('table.type_extension')

        // Export table type extension
        ->set('table.type_extension.export', Symfony\Export\TableTypeExtension::class)
            ->args([
                service('serializer'),
            ])
            ->tag('table.type_extension')

        // Doctrine ORM extension
        ->set('table.extension.doctrine.orm', DoctrineORMExtension::class)
            ->args([
                service('doctrine'),
            ])

        // Dependency injection extension
        ->set('table.extension.dependency_injection', Symfony\DependencyInjection\DependencyInjectionExtension::class)
            ->args([
                abstract_arg('Table service locator'),
                abstract_arg('"table.type" tagged services ids'),
                abstract_arg('"table.type_extension" tagged services ids'),
                abstract_arg('"table.column_type" tagged services ids'),
                abstract_arg('"table.column_type_extension" tagged services ids'),
                abstract_arg('"table.filter_type" tagged services ids'),
                abstract_arg('"table.filter_type_extension" tagged services ids'),
                abstract_arg('"table.action_type" tagged services ids'),
                abstract_arg('"table.action_type_extension" tagged services ids'),
                abstract_arg('"table.adapter" tagged services ids'),
            ])

        // Table registry
        ->set('table.registry', TableRegistry::class)
            ->args([
                [
                    service('table.extension.dependency_injection'),
                    service('table.extension.doctrine.orm'),
                ],
                inline_service(ResolvedTypeFactory::class),
            ])
            ->alias(RegistryInterface::class, 'table.registry')

        // Table factory
        ->set('table.factory', TableTableFactory::class)
            ->args([
                service('table.registry'),
                service('form.factory'),
            ])
            ->alias(TableFactoryInterface::class, 'table.factory')->public()

        // Twig renderer
        ->set(TableRenderer::class)
            ->args([
                service('twig'),
                service('pagerfanta.view_factory'),
            ])
            ->tag('twig.runtime')

        // Twig extension
        ->set('table.twig.extension', TableExtension::class)
            ->tag('twig.extension')
    ;
};
