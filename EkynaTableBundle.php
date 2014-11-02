<?php

namespace Ekyna\Bundle\TableBundle;

use Ekyna\Bundle\TableBundle\DependencyInjection\Compiler\TablePass;
use Ekyna\Component\Table\DependencyInjection\Compiler\TwigPathCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EkynaTableBundle
 * @package Ekyna\Bundle\TableBundle
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaTableBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TwigPathCompilerPass());
        $container->addCompilerPass(new TablePass());
    }
}
