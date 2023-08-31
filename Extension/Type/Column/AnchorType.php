<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Bundle\TableBundle\Model\Anchor;
use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Column\ColumnInterface;
use Ekyna\Component\Table\Extension\Core\Type\Column\TextType;
use Ekyna\Component\Table\Source\RowInterface;
use Ekyna\Component\Table\View\CellView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use function array_diff_key;
use function count;

/**
 * Class AnchorType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class AnchorType extends AbstractColumnType
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function buildCellView(CellView $view, ColumnInterface $column, RowInterface $row, array $options): void
    {
        if (isset($view->vars['anchor'])) {
            return;
        }

        if (null === $value = $view->vars['value']) {
            return;
        }

        $view->vars['anchor'] = new Anchor($value, (string)$value);

        if (!isset($view->vars['route'])) {
            return;
        }

        $parameters = $options['parameters'];
        if (!empty($options['parameters_map'])) {
            //$prefix =
            foreach ($options['parameters_map'] as $parameter => $propertyPath) {
                if (null !== $value = $row->getData($propertyPath)) {
                    $parameters[$parameter] = $value;
                }
            }

            if (0 < count(array_diff_key($options['parameters_map'], $parameters))) {
                return;
            }
        }

        $view->vars['anchor']->attr['href'] = $this->urlGenerator->generate($options['route'], $parameters);
    }

    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'route'          => null,
                'parameters'     => [],
                'parameters_map' => [],
            ])
            ->setAllowedTypes('route', ['string', 'null'])
            ->setAllowedTypes('parameters', 'array')
            ->setAllowedTypes('parameters_map', 'array');
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return TextType::class;
    }
}
