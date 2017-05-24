<?php

namespace Ekyna\Bundle\TableBundle\Extension;

use Ekyna\Component\Table\Extension\AbstractTableExtension;

/**
 * Class TableExtension
 * @package Ekyna\Bundle\TableBundle\Extension
 * @author  Etienne Dauvergne <contact@ekyna.com>
 */
class TableExtension extends AbstractTableExtension
{
    /**
     * @inheritDoc
     */
    protected function loadColumnTypes()
    {
        return [
            new Type\Column\ActionsType,
            new Type\Column\AnchorType,
            new Type\Column\CountryType,
            new Type\Column\NestedActionsType,
            new Type\Column\NestedAnchorType,
            new Type\Column\PriceType,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function loadFilterTypes()
    {
        return [
            new Type\Filter\CountryType,
        ];
    }
}
