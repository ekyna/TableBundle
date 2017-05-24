<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\TextType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AnchorType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AnchorType extends AbstractColumnType
{
    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options)
    {
        $parameters = $options['route_parameters'];
        if (!empty($options['route_parameters_map'])) {
            foreach ($options['route_parameters_map'] as $parameter => $propertyPath) {
                if (null !== $value = $row->getData($propertyPath)) {
                    $parameters[$parameter] = $value;
                }
            }

            if (0 < count(array_diff_key($options['route_parameters_map'], $parameters))) {
                return;
            }
        }

        $view->vars['route'] = $options['route_name'];
        $view->vars['parameters'] = $parameters;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('route_name')
            ->setDefaults([
                'route_parameters'     => [],
                'route_parameters_map' => [],
            ])
            ->setAllowedTypes('route_name', 'string')
            ->setAllowedTypes('route_parameters', 'array')
            ->setAllowedTypes('route_parameters_map', 'array');
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return TextType::class;
    }
}
