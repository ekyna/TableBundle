<?php

namespace Ekyna\Bundle\TableBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ekyna\Bundle\TableBundle\DependencyInjection\Compiler\TablePass;

class EkynaTableBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new TablePass());
    }
}
