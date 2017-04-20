<?php

declare(strict_types=1);

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
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $builder = new TreeBuilder('ekyna_table');

        $root = $builder->getRootNode();

        $root
            ->children()
                ->scalarNode('template')->defaultValue('@EkynaTable/table.html.twig')->end()
            ->end();

        return $builder;
    }
}
