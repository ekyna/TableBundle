<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Context\ActiveFilter;
use Ekyna\Component\Table\Extension\AbstractFilterTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Filter\FilterType;
use Ekyna\Component\Table\Filter\FilterInterface;
use Ekyna\Component\Table\View\ActiveFilterView;
use Ekyna\Component\Table\View\AvailableFilterView;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_replace;

/**
 * Class TranslateFilterTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TranslateFilterTypeExtension extends AbstractFilterTypeExtension
{
    use TranslateTrait;

    public function buildAvailableFilterView(AvailableFilterView $view, FilterInterface $filter, array $options): void
    {
        $view->vars = array_replace($view->vars, [
            'trans_domain' => $options['trans_domain'],
        ]);
    }

    public function buildActiveFilterView(
        ActiveFilterView $view,
        FilterInterface $filter,
        ActiveFilter $activeFilter,
        array $options
    ): void {
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
        return [FilterType::class];
    }
}
