<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\PropertyType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class PriceType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class PriceType extends AbstractColumnType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'currency'      => null,
                'currency_path' => null,
                'alignment'     => 'right',
            ])
            ->setAllowedTypes('currency', ['null', 'string'])
            ->setAllowedTypes('currency_path', ['null', 'string']);
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        if (!empty($path = $options['currency_path'])) {
            $currency = $row->getData($path);
        } else {
            $currency = $options['currency'];
        }

        $view->vars['currency'] = $currency;
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return PropertyType::class;
    }
}
