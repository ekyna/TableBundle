<?php

namespace Ekyna\Bundle\TableBundle;

use Ekyna\Bundle\TableBundle\DependencyInjection\Compiler\TablePass;
use Ekyna\Component\Table\Bridge\Symfony\DependencyInjection\TwigPathCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
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
        $container->addCompilerPass(new TablePass(), PassConfig::TYPE_BEFORE_REMOVING);
    }
}
