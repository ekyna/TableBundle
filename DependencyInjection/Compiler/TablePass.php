<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\DependencyInjection\Compiler;

use Ekyna\Component\Table\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PriorityTaggedServiceTrait;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_key_exists;

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
    public const TABLE_TYPE            = 'table.type';
    public const TABLE_TYPE_EXTENSION  = 'table.type_extension';
    public const COLUMN_TYPE           = 'table.column_type';
    public const COLUMN_TYPE_EXTENSION = 'table.column_type_extension';
    public const FILTER_TYPE           = 'table.filter_type';
    public const FILTER_TYPE_EXTENSION = 'table.filter_type_extension';
    public const ACTION_TYPE           = 'table.action_type';
    public const ACTION_TYPE_EXTENSION = 'table.action_type_extension';
    public const ADAPTER_FACTORY       = 'table.adapter_factory';

    use PriorityTaggedServiceTrait;

    private ContainerBuilder $container;
    private array $services;


    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('table.extension.dependency_injection');

        $this->container = $container;
        $this->services = [];

        $definition->replaceArgument(1, $this->loadTypes(self::TABLE_TYPE));
        $definition->replaceArgument(2, $this->loadTypeExtensions(self::TABLE_TYPE_EXTENSION));
        $definition->replaceArgument(3, $this->loadTypes(self::COLUMN_TYPE));
        $definition->replaceArgument(4, $this->loadTypeExtensions(self::COLUMN_TYPE_EXTENSION));
        $definition->replaceArgument(5, $this->loadTypes(self::FILTER_TYPE));
        $definition->replaceArgument(6, $this->loadTypeExtensions(self::FILTER_TYPE_EXTENSION));
        $definition->replaceArgument(7, $this->loadTypes(self::ACTION_TYPE));
        $definition->replaceArgument(8, $this->loadTypeExtensions(self::ACTION_TYPE_EXTENSION));
        $definition->replaceArgument(9, $this->loadTypes(self::ADAPTER_FACTORY));

        $definition->replaceArgument(0, ServiceLocatorTagPass::register($container, $this->services, 'ekyna_table'));
    }

    /**
     * Loads types services.
     *
     * @param string $typeTag
     *
     * @return array
     */
    private function loadTypes(string $typeTag): array
    {
        $types = [];

        foreach ($this->container->findTaggedServiceIds($typeTag, true) as $serviceId => $tags) {
            $class = $this->container->getDefinition($serviceId)->getClass();

            if (array_key_exists($class, $types)) {
                throw new InvalidArgumentException(
                    "Table type '$class' is already registered with alias '$serviceId'."
                );
            }

            $types[$class] = $serviceId;

            $this->services[$serviceId] = new Reference($serviceId);
        }

        return $types;
    }

    /**
     * Loads type extensions services.
     *
     * @param string $extensionTag
     *
     * @return array
     */
    private function loadTypeExtensions(string $extensionTag): array
    {
        $extensions = [];

        foreach ($this->findAndSortTaggedServices($extensionTag, $this->container) as $reference) {
            $serviceId = (string)$reference;
            $definition = $this->container->getDefinition($serviceId);

            $class = $definition->getClass();
            $tag = $definition->getTag($extensionTag);

            if (isset($tag[0]['extended_type'])) {
                $extensions[$tag[0]['extended_type']][] = $serviceId;
            } else {
                $extendsTypes = false;

                /** @noinspection PhpUndefinedMethodInspection */
                foreach ($class::getExtendedTypes() as $extendedType) {
                    $extensions[$extendedType][] = $serviceId;
                    $extendsTypes = true;
                }

                if (!$extendsTypes) {
                    throw new InvalidArgumentException(sprintf(
                        'The getExtendedTypes() method for service "%s" does not return any extended types.',
                        $serviceId
                    ));
                }
            }

            $this->services[$serviceId] = new Reference($serviceId);
        }

        return $extensions;
    }
}
