<?php

namespace Ekyna\Bundle\TableBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class TwigPass
 * @package Ekyna\Bundle\TableBundle\DependencyInjection\Compiler
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class TwigPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('twig.loader.filesystem')) {
            $loader = $container->getDefinition('twig.loader.filesystem');

            $path = realpath(__DIR__.'/../../Resources/views/Table');
            $loader->addMethodCall('addPath', array($path));
        }
    }
}
