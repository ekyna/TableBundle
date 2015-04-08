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

        $refClass = new \ReflectionClass('Ekyna\Component\Table\Table');
        $defaultResourcePath = dirname($refClass->getFileName()).'/Resources/views/Table';

        $rootNode
            ->children()
                // TODO remove : deprecated
                ->scalarNode('template')->defaultValue('EkynaTableBundle::table.html.twig')->end()
                ->arrayNode('templating')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('resource')
                    ->children()
                        ->arrayNode('resources')
                            ->prototype('scalar')->defaultValue($defaultResourcePath)->end()
                            ->validate()
                                ->ifTrue(function ($v) use ($defaultResourcePath) {
                                    return !in_array($defaultResourcePath, $v);
                                })
                                ->then(function ($v) use ($defaultResourcePath) {
                                    return array_merge(array($defaultResourcePath), $v);
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('twig')
                    ->addDefaultsIfNotSet()
                    ->fixXmlConfig('resource')
                    ->children()
                        ->arrayNode('resources')
                            ->prototype('scalar')->defaultValue('table_layout.html.twig')->end()
                            ->example(array('MyBundle::table.html.twig'))
                            ->validate()
                                ->ifTrue(function ($v) {return !in_array('table_layout.html.twig', $v); })
                                ->then(function ($v) {
                                    return array_merge(array('table_layout.html.twig'), $v);
                                })
                            ->end()
                        ->end()
                    ->end()
                ->end()
                // TODO attribute_bag_name
            ->end()
        ;

        return $treeBuilder;
    }
}
