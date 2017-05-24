<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Doctrine\Common\Collections\Collection;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class NestedAnchorType
 * @package Ekyna\Component\Table\Extension\Core\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class NestedAnchorType extends AbstractColumnType
{
    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessorInterface
     */
    private $propertyAccessor;


    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'left_property_path'     => 'left',
                'right_property_path'    => 'right',
                'parent_property_path'   => 'parent',
                'children_property_path' => 'children',
                'cell_attr'              => ['class' => 'nested'],
            ])
            ->setAllowedTypes('left_property_path', 'string')
            ->setAllowedTypes('right_property_path', 'string')
            ->setAllowedTypes('parent_property_path', 'string')
            ->setAllowedTypes('children_property_path', 'string');
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options)
    {
        $this->propertyAccessor = $row->getPropertyAccessor();

        $data = $row->getData();

        $nodes = $this->getTreeNodes($data, $options);

        $view->vars['nodes'] = $nodes;
    }

    /**
     * @inheritdoc
     */
    private function getTreeNodes($data, array $options, $level = 0)
    {
        $nodes = [];

        if (null !== $parent = $this->propertyAccessor->getValue($data, $options['parent_property_path'])) {
            $nodes = $this->getTreeNodes($parent, $options, $level + 1);

            $left = $this->propertyAccessor->getValue($data, $options['left_property_path']);
            $right = $this->propertyAccessor->getValue($data, $options['right_property_path']);
            $parentRight = $this->propertyAccessor->getValue($parent, $options['right_property_path']);

            $type = 'node';
            $classes = $childrenIds = [];

            $isLast = $right === $parentRight - 1;

            if ($level === 0) {
                if ($right - $left > 1) {
                    $type = 'button';
                    $classes[] = 'toggle';
                    $classes[] = 'toggle-close';
                    $childrenIds = $this->getChildrenIds($data, $options);
                } else {
                    $classes[] = 'child';
                }
                if ($isLast) {
                    $classes[] = 'last';
                }
            } elseif (!$isLast) {
                $classes[] = 'continue';
            }

            $nodes[] = [
                'type'     => $type,
                'class'    => implode(' ', $classes),
                'children' => json_encode($childrenIds),
            ];
        }

        return $nodes;
    }

    /**
     * Returns the children ids.
     *
     * @param mixed $data
     * @param array $options
     *
     * @return array
     */
    private function getChildrenIds($data, array $options)
    {
        $children = $this->propertyAccessor->getValue($data, $options['children_property_path']);
        if ($children instanceof Collection) {
            $children = $children->toArray();
            if (!empty($children)) {
                return array_map(function ($child) {
                    return $this->propertyAccessor->getValue($child, 'id');
                }, $children);
            }
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return AnchorType::class;
    }
}
