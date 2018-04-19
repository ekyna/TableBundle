<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnBuilderInterface;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\ColumnType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ActionsType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ActionsType extends AbstractColumnType
{
    /**
     * @var OptionsResolver
     */
    private $buttonOptionsResolver;

    /**
     * @var \Closure
     */
    private $buttonBuilder;


    /**
     * @inheritDoc
     */
    public function buildColumn(ColumnBuilderInterface $builder, array $options = [])
    {
        $builder
            ->setSortable(false)
            ->setPropertyPath(null);
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options)
    {
        $buttons = isset($view->vars['buttons']) ? $view->vars['buttons'] : [];

        $builder = $options['button_builder'];

        foreach ($options['buttons'] as $buttonOptions) {
            if (null !== $button = $builder($row, $buttonOptions)) {
                $buttons[] = $button;
            }
        }

        $view->vars['buttons'] = $buttons;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        /** @noinspection PhpUnusedParameterInspection */
        $resolver
            ->setDefaults([
                'label'           => null,
                'position'        => 999,
                'buttons'         => [],
                'button_resolver' => $this->getButtonOptionsResolver(),
                'button_builder'  => $this->getButtonBuilder(),
                'cell_attr'       => ['class' => 'actions'],
            ])
            ->setAllowedTypes('buttons', 'array')
            ->setAllowedTypes('button_resolver', OptionsResolver::class)
            ->setAllowedTypes('button_builder', \Closure::class)
            ->setNormalizer('buttons', function (Options $options, $value) {
                if (empty($value)) {
                    return $value;
                }

                $resolver = $options['button_resolver'];

                $buttons = [];
                foreach ($value as $buttonOptions) {
                    $buttons[] = $resolver->resolve($buttonOptions);
                }

                return $buttons;
            });
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ColumnType::class;
    }

    /**
     * Returns the button options resolver.
     *
     * @return OptionsResolver
     */
    private function getButtonOptionsResolver()
    {
        if (null !== $this->buttonOptionsResolver) {
            return $this->buttonOptionsResolver;
        }

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired([
                'label',
                'route_name',
                'route_parameters_map',
            ])
            ->setDefaults([
                'route_parameters'      => [],
                'class'                 => 'default',
                'icon'                  => null,
                'confirm'               => null,
                'target'                => null,
                'disabled'              => false,
                'disable_property_path' => null,
                'disable'               => null,
                'filter'                => null,
            ])
            ->setAllowedTypes('label', 'string')
            ->setAllowedTypes('route_name', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedTypes('route_parameters_map', 'array')
            ->setAllowedTypes('class', ['null', 'string'])
            ->setAllowedTypes('icon', ['null', 'string'])
            ->setAllowedTypes('confirm', ['null', 'string'])
            ->setAllowedTypes('target', ['null', 'string'])
            ->setAllowedTypes('disabled', 'bool')
            ->setAllowedTypes('disable_property_path', ['null', 'string'])
            ->setAllowedTypes('disable', ['null', 'callable'])
            ->setAllowedTypes('filter', ['null', 'callable']);

        return $this->buttonOptionsResolver = $resolver;
    }

    /**
     * Returns the button builder.
     *
     * @return \Closure
     */
    private function getButtonBuilder()
    {
        if (null !== $this->buttonBuilder) {
            return $this->buttonBuilder;
        }

        return $this->buttonBuilder = function (RowInterface $row, array $buttonOptions) {
            if ((null !== $filter = $buttonOptions['filter']) && is_callable($filter) && !$filter($row)) {
                return null;
            }

            $parameters = $buttonOptions['route_parameters'];
            foreach ($buttonOptions['route_parameters_map'] as $parameter => $propertyPath) {
                $parameters[$parameter] = $row->getData($propertyPath);
            }

            $disabled = $buttonOptions['disabled'];
            if (!empty($disabledPath = $buttonOptions['disable_property_path'])) {
                $disabled = $row->getData($disabledPath);
            } elseif ((null !== $disable = $buttonOptions['disable']) && is_callable($disable)) {
                $disabled = $disable($row);
            }

            return [
                'label'      => $buttonOptions['label'],
                'class'      => $buttonOptions['class'],
                'icon'       => $buttonOptions['icon'],
                'confirm'    => $buttonOptions['confirm'],
                'target'     => $buttonOptions['target'],
                'route'      => $buttonOptions['route_name'],
                'parameters' => $parameters,
                'disabled'   => $disabled,
            ];
        };
    }
}
