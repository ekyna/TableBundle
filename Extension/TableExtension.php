<?php

declare(strict_types=1);

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
    protected function loadColumnTypes(): array
    {
        return [
            new Type\Column\ActionsType(),
            new Type\Column\CountryType(),
            new Type\Column\LocaleType(),
            new Type\Column\NestedActionsType(),
            new Type\Column\NestedAnchorType(),
            new Type\Column\PriceType(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function loadFilterTypes(): array
    {
        return [
            new Type\Filter\CountryType(),
        ];
    }

    /**
     * @inheritDoc
     */
    protected function loadTableTypeExtensions(): array
    {
        return [
            new Type\Extension\BootstrapTypeExtension(),
        ];
    }
}
