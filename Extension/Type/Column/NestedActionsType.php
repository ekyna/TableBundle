<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Bridge\Doctrine\ORM\Source as ORM;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Source as Core;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\TableInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NestedActionsType
 * @package Ekyna\Component\Table\Extension\Core\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class NestedActionsType extends AbstractColumnType
{
    /**
     * Trees right bounds cache.
     *
     * @var array
     */
    static $rightBounds;


    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options)
    {
        $disabled = false;
        if (!empty($disabledPath = $options['disable_property_path'])) {
            $disabled = $row->getData($disabledPath);
        }

        $newChildButton = $moveUpButton = $moveDownButton = [
            'disabled' => $disabled,
            'label'    => 'Ajouter', // TODO translation
            'class'    => 'primary',
            'confirm'  => null,
            'target'   => null,
        ];

        $newChildButton['icon'] = 'plus';
        $newChildButton['class'] = 'success';

        $moveUpButton['icon'] = 'arrow-up';
        $moveUpButton['label'] = 'Déplacer vers le haut'; // TODO translation

        $moveDownButton['icon'] = 'arrow-down';
        $moveDownButton['label'] = 'Déplacer vers le bas'; // TODO translation

        if (!$disabled) {
            $parameters = $options['routes_parameters'];
            foreach ($options['routes_parameters_map'] as $parameter => $propertyPath) {
                $parameters[$parameter] = $row->getData($propertyPath);
            }

            $newChildButton['route'] = $options['new_child_route'];
            $newChildButton['parameters'] = $parameters;

            $left = $row->getData($options['left_property_path']);
            $right = $row->getData($options['right_property_path']);
            $parent = $row->getData($options['parent_property_path']);

            if ($options['roots'] && null === $parent) {
                // Roots can't be moved
                $moveUpButton['disabled'] = true;
                $moveDownButton['disabled'] = true;
            } else {
                if (null !== $parent) {
                    $accessor = $row->getPropertyAccessor();
                    $parentLeft = $accessor->getValue($parent, $options['left_property_path']);
                    $parentRight = $accessor->getValue($parent, $options['right_property_path']);
                } else {
                    $parentLeft = 0;
                    $parentRight = $this->getRightBound($column->getTable(), $options) + 1;
                }

                if ($left === $parentLeft + 1) {
                    $moveUpButton['disabled'] = true;
                } else {
                    $moveUpButton['route'] = $options['move_up_route'];
                    $moveUpButton['parameters'] = $parameters;
                }

                if ($right === $parentRight - 1) {
                    $moveDownButton['disabled'] = true;
                } else {
                    $moveDownButton['route'] = $options['move_down_route'];
                    $moveDownButton['parameters'] = $parameters;
                }
            }
        }

        array_unshift($view->vars['buttons'], $moveDownButton);
        array_unshift($view->vars['buttons'], $moveUpButton);
        array_unshift($view->vars['buttons'], $newChildButton);
    }

    /**
     * Returns the right bound for the current tree.
     *
     * @param TableInterface $table
     * @param array          $options
     *
     * @return int
     */
    private function getRightBound(TableInterface $table, array $options)
    {
        if (isset(static::$rightBounds[$table->getHash()])) {
            return static::$rightBounds[$table->getHash()];
        }

        $rightBound = 0;

        $source = $table->getConfig()->getSource();
        $adapter = $table->getSourceAdapter();
        $property = $options['right_property_path'];

        if ($adapter instanceof Core\ArrayAdapter) {
            /** @var Core\ArraySource $source */
            $data = $source->getData();
            foreach ($data as $datum) {
                if ($datum[$property] > $rightBound) {
                    $rightBound = $datum[$property];
                }
            }
        } elseif ($adapter instanceof ORM\EntityAdapter) {
            /** @var ORM\EntitySource $source */
            $qb = $adapter->getManager()->createQueryBuilder();
            $rightBound = $qb
                ->from($source->getClass(), 'o')
                ->select('MAX(o.' . $property . ')')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return static::$rightBounds[$table->getHash()] = $rightBound;
    }

    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'roots',
                'move_up_route',
                'move_down_route',
                'new_child_route',
                'routes_parameters_map',
            ])
            ->setDefaults([
                'routes_parameters'     => [],
                'left_property_path'    => 'left',
                'right_property_path'   => 'right',
                'parent_property_path'  => 'parent',
                'disable_property_path' => '',
            ])
            ->setAllowedTypes('roots', 'boolean')
            ->setAllowedTypes('move_up_route', 'string')
            ->setAllowedTypes('move_down_route', 'string')
            ->setAllowedTypes('new_child_route', 'string')
            ->setAllowedTypes('routes_parameters_map', 'array')
            ->setAllowedTypes('routes_parameters', 'array')
            ->setAllowedTypes('left_property_path', 'string')
            ->setAllowedTypes('right_property_path', 'string')
            ->setAllowedTypes('parent_property_path', 'string')
            ->setAllowedTypes('disable_property_path', 'string');
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ActionsType::class;
    }
}
