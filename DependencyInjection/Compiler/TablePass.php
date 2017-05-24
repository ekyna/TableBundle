<?php

namespace Ekyna\Bundle\TableBundle\DependencyInjection\Compiler;

use Ekyna\Component\Table\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class TablePass
 *
 * Adds all services with the tags :
 *  - table.type
 *  - table.type_extension
 *  - table.column_type
 *  - table.column_type_extension
 *  - table.filter_type
 *  - table.filter_type_extension
 *  - table.action_type
 *  - table.action_type_extension
 *  - table.adapter_factory
 * as arguments of the "table.extension" service
 *
 * @package Ekyna\Bundle\TableBundle\DependencyInjection\Compiler
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TablePass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('table.extension')) {
            return;
        }

        $definition = $container->getDefinition('table.extension');


        // table.type
        $types = [];

        foreach ($container->findTaggedServiceIds('table.type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as table types are lazy-loaded.',
                    $serviceId
                ));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }

        $definition->replaceArgument(1, $types);


        // table.type_extension
        $typeExtensions = array();

        foreach ($this->findAndSortTaggedServices('table.type_extension', $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as table type extensions are lazy-loaded.',
                    $serviceId
                ));
            }

            $tag = $serviceDefinition->getTag('table.type_extension');
            if (isset($tag[0]['extended_type'])) {
                $extendedType = $tag[0]['extended_type'];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Tagged table type extension must have the extended type configured using the '.
                    'extended_type/extended-type attribute, none was configured for the "%s" service.',
                    $serviceId
                ));
            }

            $typeExtensions[$extendedType][] = $serviceId;
        }

        $definition->replaceArgument(2, $typeExtensions);


        // table.column_type
        $types = [];
        foreach ($container->findTaggedServiceIds('table.column_type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as column types are lazy-loaded.',
                    $serviceId
                ));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }

        $definition->replaceArgument(3, $types);


        // table.column_type_extension
        $typeExtensions = array();

        foreach ($this->findAndSortTaggedServices('table.column_type_extension', $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as table type column extensions are lazy-loaded.',
                    $serviceId
                ));
            }

            $tag = $serviceDefinition->getTag('table.column_type_extension');
            if (isset($tag[0]['extended_type'])) {
                $extendedType = $tag[0]['extended_type'];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Tagged column type extension must have the extended type configured using the '.
                    'extended_type/extended-type attribute, none was configured for the "%s" service.',
                    $serviceId
                ));
            }

            $typeExtensions[$extendedType][] = $serviceId;
        }

        $definition->replaceArgument(4, $typeExtensions);


        // table.filter_type
        $types = [];
        foreach ($container->findTaggedServiceIds('table.filter_type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as filter types are lazy-loaded.',
                    $serviceId
                ));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(5, $types);


        // table.filter_type_extension
        $typeExtensions = array();

        foreach ($this->findAndSortTaggedServices('table.filter_type_extension', $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as table filter type extensions are lazy-loaded.',
                    $serviceId
                ));
            }

            $tag = $serviceDefinition->getTag('table.filter_type_extension');
            if (isset($tag[0]['extended_type'])) {
                $extendedType = $tag[0]['extended_type'];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Tagged filter type extension must have the extended type configured using the '.
                    'extended_type/extended-type attribute, none was configured for the "%s" service.',
                    $serviceId
                ));
            }

            $typeExtensions[$extendedType][] = $serviceId;
        }

        $definition->replaceArgument(6, $typeExtensions);


        // table.action_type
        $types = [];
        foreach ($container->findTaggedServiceIds('table.action_type') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as action types are lazy-loaded.',
                    $serviceId
                ));
            }

            // Support type access by FQCN
            $types[$serviceDefinition->getClass()] = $serviceId;
        }
        $definition->replaceArgument(7, $types);


        // table.action_type_extension
        $typeExtensions = array();

        foreach ($this->findAndSortTaggedServices('table.action_type_extension', $container) as $reference) {
            $serviceId = (string) $reference;
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as table action type extensions are lazy-loaded.',
                    $serviceId
                ));
            }

            $tag = $serviceDefinition->getTag('table.action_type_extension');
            if (isset($tag[0]['extended_type'])) {
                $extendedType = $tag[0]['extended_type'];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Tagged action type extension must have the extended type configured using the '.
                    'extended_type/extended-type attribute, none was configured for the "%s" service.',
                    $serviceId
                ));
            }

            $typeExtensions[$extendedType][] = $serviceId;
        }

        $definition->replaceArgument(8, $typeExtensions);

        // table.adapter_factory
        $adapters = [];
        foreach ($container->findTaggedServiceIds('table.adapter_factory') as $serviceId => $tag) {
            $serviceDefinition = $container->getDefinition($serviceId);
            if (!$serviceDefinition->isPublic()) {
                throw new InvalidArgumentException(sprintf(
                    'The service "%s" must be public as adapter factories are lazy-loaded.',
                    $serviceId
                ));
            }

            // Support type access by FQCN
            $adapters[$serviceDefinition->getClass()] = $serviceId;
        }

        $definition->replaceArgument(9, $adapters);
    }

    /**
     * Finds all services with the given tag name and order them by their priority.
     *
     * The order of additions must be respected for services having the same priority,
     * and knowing that the \SplPriorityQueue class does not respect the FIFO method,
     * we should not use this class.
     *
     * @see https://bugs.php.net/bug.php?id=53710
     * @see https://bugs.php.net/bug.php?id=60926
     *
     * @param string           $tagName
     * @param ContainerBuilder $container
     *
     * @return Reference[]
     *
     * @todo use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait; (SF 3.2)
     */
    private function findAndSortTaggedServices($tagName, ContainerBuilder $container)
    {
        $services = array();

        foreach ($container->findTaggedServiceIds($tagName) as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $priority = isset($attributes['priority']) ? $attributes['priority'] : 0;
                $services[$priority][] = new Reference($serviceId);
            }
        }

        if ($services) {
            krsort($services);
            $services = call_user_func_array('array_merge', $services);
        }

        return $services;
    }
}
