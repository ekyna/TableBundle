<?php

namespace Ekyna\Bundle\TableBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Ekyna\Bundle\TableBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ekyna_table');

        $rootNode
            ->children()
                ->scalarNode('template')->defaultValue('EkynaTableBundle::table.html.twig')->end()
                // TODO attribute_bag_name
            ->end()
        ;

        return $treeBuilder;
    }
}
