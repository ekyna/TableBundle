<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Closure;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnBuilderInterface;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Exception\InvalidArgumentException;
use Ekyna\Component\Table\Extension\Core\Type\Column\ColumnType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use ReflectionFunction;
use Symfony\Component\OptionsResolver\Exception\InvalidArgumentException as InvalidOptions;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_replace;
use function count;
use function is_array;
use function is_callable;
use function Symfony\Component\Translation\t;

/**
 * Class ActionsType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ActionsType extends AbstractColumnType
{
    public const BUTTON_DEFAULTS = [
        'path'           => null,
        'route'          => null,
        'parameters'     => [],
        'parameters_map' => [],
        'theme'          => 'default',
        'icon'           => null,
        'fa_icon'        => false,
        'confirm'        => null,
        'target'         => null,
        'disabled'       => false,
        'disable'        => null,
        'filter'         => null,
        'trans_domain'   => null,
    ];

    private ?OptionsResolver $buttonOptionsResolver = null;
    private ?Closure         $buttonBuilder         = null;

    public function buildColumn(ColumnBuilderInterface $builder, array $options = []): void
    {
        $builder
            ->setSortable(false)
            ->setPropertyPath(null);
    }

    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        $buttons = $view->vars['buttons'] ?? [];

        $builder = $options['button_builder'];

        foreach ($options['buttons'] as $buttonOptions) {
            if (is_callable($buttonOptions)) {
                if (null !== $button = $buttonOptions($row)) {
                    $buttons[] = array_replace(self::BUTTON_DEFAULTS, $button);
                }
            } elseif (null !== $button = $builder($row, $buttonOptions)) {
                $buttons[] = $button;
            }
        }

        $view->vars['buttons'] = $buttons;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'label'           => t('actions', [], 'EkynaTable'),
                'position'        => 999,
                'buttons'         => [],
                'buttons_loader'  => null,
                'button_resolver' => $this->getButtonOptionsResolver(),
                'button_builder'  => $this->getButtonBuilder(),
                'cell_attr'       => ['class' => 'actions'],
            ])
            ->setAllowedTypes('buttons', 'array')
            ->setAllowedTypes('buttons_loader', [Closure::class, 'null'])
            ->setAllowedTypes('button_resolver', OptionsResolver::class)
            ->setAllowedTypes('button_builder', Closure::class)
            ->setNormalizer('buttons', function (Options $options, $value) {
                $loader = $options['buttons_loader'];
                if (is_callable($loader) && $this->checkButtonsLoader($loader)) {
                    $value = $loader($options, $value);
                }

                if (empty($value)) {
                    return $value;
                }

                $resolver = $options['button_resolver'];

                $buttons = [];
                foreach ($value as $button) {
                    if (is_callable($button) && $this->checkButtonBuilder($button)) {
                        $buttons[] = $button;
                    } elseif (is_array($button)) {
                        $buttons[] = $resolver->resolve($button);
                    } else {
                        throw new InvalidArgumentException('Button options must be an array of options or a closure.');
                    }
                }

                return $buttons;
            });
    }

    public function getParent(): ?string
    {
        return ColumnType::class;
    }

    /**
     * Checks the custom buttons loader callable signature.
     *
     * @param callable $callable
     *
     * @return bool
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function checkButtonsLoader(callable $callable): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();

        if (2 !== count($parameters)) {
            return false;
        }

        if (null === $class = $parameters[0]->getClass()) {
            return false;
        }

        if ($class->name !== Options::class) {
            return false;
        }

        /* TODO if (null === $type = $parameters[1]->getType()) {
            return false;
        }
        if ($type->getName() !== 'array') {
            return false;
        }*/

        return true;
    }

    /**
     * Checks the custom button builder callable signature.
     *
     * @param callable $callable
     *
     * @return bool
     *
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function checkButtonBuilder(callable $callable): bool
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $reflection = new ReflectionFunction($callable);
        $parameters = $reflection->getParameters();

        if (1 !== count($parameters)) {
            return false;
        }

        if (null === $class = $parameters[0]->getClass()) {
            return false;
        }

        if ($class->name !== RowInterface::class) {
            return false;
        }

        return true;
    }

    /**
     * Returns the button options resolver.
     */
    private function getButtonOptionsResolver(): OptionsResolver
    {
        if (null !== $this->buttonOptionsResolver) {
            return $this->buttonOptionsResolver;
        }

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired([
                'label',
            ])
            ->setDefaults([ // TODO Use BUTTON_DEFAULTS constant ?
                'path'                  => null,
                'route'                 => null,
                'parameters'            => [],
                'parameters_map'        => [],
                'theme'                 => 'default',
                'icon'                  => null,
                'fa_icon'               => false,
                'confirm'               => null,
                'target'                => null,
                'disabled'              => false,
                'disable_property_path' => null,
                'disable'               => null,
                'filter'                => null,
                'trans_domain'          => null,
            ])
            ->setAllowedTypes('label', 'string')
            ->setAllowedTypes('path', ['null', 'string'])
            ->setAllowedTypes('route', ['null', 'string'])
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('parameters_map', 'array')
            ->setAllowedTypes('theme', ['null', 'string'])
            ->setAllowedTypes('icon', ['null', 'string'])
            ->setAllowedTypes('fa_icon', 'bool')
            ->setAllowedTypes('confirm', ['null', 'string'])
            ->setAllowedTypes('target', ['null', 'string'])
            ->setAllowedTypes('disabled', 'bool')
            ->setAllowedTypes('disable_property_path', ['null', 'string'])
            ->setAllowedTypes('disable', ['null', 'callable'])
            ->setAllowedTypes('filter', ['null', 'callable'])
            ->setAllowedTypes('trans_domain', ['null', 'string'])
            ->setNormalizer('route', function (Options $options, $value) {
                if (empty($value) && empty($options['path'])) {
                    throw new InvalidOptions("The button option 'path' must be defined if 'route' is not.");
                }

                return $value;
            });

        return $this->buttonOptionsResolver = $resolver;
    }

    /**
     * Returns the button builder.
     */
    private function getButtonBuilder(): Closure
    {
        if (null !== $this->buttonBuilder) {
            return $this->buttonBuilder;
        }

        return $this->buttonBuilder = Closure::fromCallable(
            function (RowInterface $row, array $buttonOptions) {
                // Filter
                if (($filter = $buttonOptions['filter']) && is_callable($filter) && !$filter($row)) {
                    return null;
                }

                // Parameters
                foreach ($buttonOptions['parameters_map'] as $parameter => $propertyPath) {
                    $buttonOptions['parameters'][$parameter] = $row->getData($propertyPath);
                }
                unset($buttonOptions['parameters_map']);

                // Disabled
                if (!empty($disabledPath = $buttonOptions['disable_property_path'])) {
                    $buttonOptions['disabled'] = $row->getData($disabledPath);
                } elseif ((null !== $disable = $buttonOptions['disable']) && is_callable($disable)) {
                    $buttonOptions['disabled'] = $disable($row);
                }
                unset($buttonOptions['disable_property_path']);

                return $buttonOptions;
            }
        );
    }
}
