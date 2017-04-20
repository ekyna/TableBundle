<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\DependencyInjection;

use Ekyna\Component\Table\Bridge\Twig\TableRenderer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

use function array_key_exists;

/**
 * Class EkynaTableExtension
 * @package Ekyna\Bundle\TableBundle\DependencyInjection
 * @author Étienne Dauvergne <contact@ekyna.com>
 */
class EkynaTableExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('table.php');

        $container
            ->getDefinition(TableRenderer::class)
            ->setArgument(2, $config['template']);
    }

    /**
     * @inheritDoc
     */
    public function prepend(ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        if (array_key_exists('EkynaAdminBundle', $bundles)) {
            $this->configureEkynaAdminBundleBundle($container);
        }
    }

    /**
     * Configures the TwigBundle.
     *
     * @param ContainerBuilder $container
     */
    protected function configureEkynaAdminBundleBundle(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('ekyna_admin', [
            'stylesheets' => [
                'bundles/ekynatable/css/table.css',
                'bundles/ekynatable/css/theme.css',
            ],
        ]);
    }
}
