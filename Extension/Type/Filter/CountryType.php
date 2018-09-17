<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Filter;

use Ekyna\Component\Table\Extension\Core\Type\Filter\ChoiceType;
use Ekyna\Component\Table\Filter\AbstractFilterType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CountryType
 * @package Ekyna\Component\Table\Extension\Core\Type\Filter
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CountryType extends AbstractFilterType
{
    /**
     * @inheritdoc
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'choices' => Intl::getRegionBundle()->getCountryNames(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
