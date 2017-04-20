<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Extension\AbstractTableTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\TableType;
use Ekyna\Component\Table\TableInterface;
use Ekyna\Component\Table\View;

use function array_replace;

/**
 * Class BootstrapTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BootstrapTypeExtension extends AbstractTableTypeExtension
{
    public function buildView(View\TableView $view, TableInterface $table, array $options): void
    {
        $view->vars['attr'] = array_replace([
            'class' => 'table table-alt-head table-striped table-hover',
        ], $view->vars['attr']);
    }

    public static function getExtendedTypes(): array
    {
        return [TableType::class];
    }
}
