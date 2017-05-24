<?php

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Extension\Core\Type\Column\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class CountryType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class CountryType extends AbstractColumnType
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
     * @inheritDoc
     */
    public function getBlockPrefix()
    {
        return 'choice';
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return ChoiceType::class;
    }
}
