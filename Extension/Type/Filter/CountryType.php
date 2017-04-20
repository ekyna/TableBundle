<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Filter;

use Ekyna\Component\Table\Extension\Core\Type\Filter\ChoiceType;
use Ekyna\Component\Table\Filter\AbstractFilterType;
use Symfony\Component\Intl\Countries;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_flip;

/**
 * Class CountryType
 * @package Ekyna\Component\Table\Extension\Core\Type\Filter
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CountryType extends AbstractFilterType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_flip(Countries::getNames()),
        ]);
    }

    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
