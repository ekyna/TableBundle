<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Bridge\Doctrine\ORM\Source as ORM;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Source as Core;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\TableInterface;
use Ekyna\Component\Table\View\CellView;
use Ekyna\Component\Table\View\HeadView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_unshift;
use function trim;

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
     * @var array<string, int>
     */
    static array $rightBounds;

    public function buildHeadView(HeadView $view, ColumnInterface $column, array $options): void
    {
        $attr = $view->table->vars['attr'];
        if (!isset($attr['class'])) {
            $attr['class'] = '';
        }
        $attr['class'] = trim($attr['class'] . ' table-nested');
        $view->table->vars['attr'] = $attr;
    }

    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        $disabled = false;
        if (!empty($disabledPath = $options['disable_property_path'])) {
            $disabled = $row->getData($disabledPath);
        }

        $newChildButton = $moveUpButton = $moveDownButton = [
            'disabled'     => $disabled,
            'label'        => 'nested.add',
            'theme'        => 'primary',
            'confirm'      => null,
            'target'       => null,
            'fa_icon'      => false,
            'trans_domain' => 'EkynaTable',
        ];

        $newChildButton['icon'] = 'plus';
        $newChildButton['theme'] = 'success';

        $moveUpButton['icon'] = 'arrow-up';
        $moveUpButton['label'] = 'nested.move_up';

        $moveDownButton['icon'] = 'arrow-down';
        $moveDownButton['label'] = 'nested.move_down';

        if (!$disabled) {
            $parameters = $options['parameters'];
            foreach ($options['parameters_map'] as $parameter => $propertyPath) {
                $parameters[$parameter] = $row->getData($propertyPath);
            }

            $newChildButton['route'] = $options['new_child_route'];
            $newChildButton['parameters'] = $parameters;

            $left = $row->getData($options['left_property_path']);
            $right = $row->getData($options['right_property_path']);
            $parent = $row->getData($options['parent_property_path']);

            if ($options['roots'] && (null === $parent)) {
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
     * @noinspection PhpDocMissingThrowsInspection
     */
    private function getRightBound(TableInterface $table, array $options): int
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
            /** @noinspection PhpUnhandledExceptionInspection */
            $rightBound = (int)$qb
                ->from($source->getClass(), 'o')
                ->select('MAX(o.' . $property . ')')
                ->getQuery()
                ->getSingleScalarResult();
        }

        return static::$rightBounds[$table->getHash()] = $rightBound;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired([
                'roots',
            ])
            ->setDefaults([
                'move_up_route'         => null,
                'move_down_route'       => null,
                'new_child_route'       => null,
                'parameters_map'        => [],
                'parameters'            => [],
                'left_property_path'    => 'left',
                'right_property_path'   => 'right',
                'parent_property_path'  => 'parent',
                'disable_property_path' => '',
            ])
            ->setAllowedTypes('roots', 'boolean')
            ->setAllowedTypes('move_up_route', ['string', 'null'])
            ->setAllowedTypes('move_down_route', ['string', 'null'])
            ->setAllowedTypes('new_child_route', ['string', 'null'])
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('parameters_map', 'array')
            ->setAllowedTypes('left_property_path', 'string')
            ->setAllowedTypes('right_property_path', 'string')
            ->setAllowedTypes('parent_property_path', 'string')
            ->setAllowedTypes('disable_property_path', 'string');
    }

    public function getParent(): ?string
    {
        return ActionsType::class;
    }
}
