<?php

namespace Ekyna\Bundle\TableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

/**
 * Adds all services with the tags "table.type", "table.column_type" and "table.filter_type" as
 * arguments of the "table.extension" service
 */
class TablePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('table.extension')) {
            return;
        }

        $definition = $container->getDefinition('table.extension');

        // table.type
        $types = array();
        foreach ($container->findTaggedServiceIds('table.type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $types[$alias] = $serviceId;
        }
        $definition->replaceArgument(1, $types);

        // table.column_type
        $types = array();
        foreach ($container->findTaggedServiceIds('table.column_type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $types[$alias] = $serviceId;
        }
        $definition->replaceArgument(2, $types);

        // table.filter_type
        $types = array();
        foreach ($container->findTaggedServiceIds('table.filter_type') as $serviceId => $tag) {
            $alias = isset($tag[0]['alias']) ? $tag[0]['alias'] : $serviceId;
            $types[$alias] = $serviceId;
        }
        $definition->replaceArgument(3, $types);
    }
}
