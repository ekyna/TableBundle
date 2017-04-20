<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\AbstractColumnTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Column\ColumnType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Ekyna\Component\Table\View\HeadView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_replace;

/**
 * Class TranslateColumnTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TranslateColumnTypeExtension extends AbstractColumnTypeExtension
{
    use TranslateTrait;

    public function buildHeadView(HeadView $view, ColumnInterface $column, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'trans_domain' => $options['trans_domain'],
        ]);
    }

    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'trans_domain' => $options['trans_domain'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->configureTranslationOptions($resolver);
    }

    public static function getExtendedTypes(): array
    {
        return [ColumnType::class];
    }
}
