<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Column;

use Ekyna\Component\Table\Column\AbstractColumnType;
use Ekyna\Component\Table\Extension\Core\Type\Column\ChoiceType;
use Symfony\Component\Intl\Locales;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function array_flip;

/**
 * Class LocaleType
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Column
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class LocaleType extends AbstractColumnType
{
    /**
     * @inheritDoc
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => array_flip(Locales::getNames()),
        ]);
    }

    /**
     * @inheritDoc
     */
    public function getBlockPrefix(): string
    {
        return 'choice';
    }

    /**
     * @inheritDoc
     */
    public function getParent(): ?string
    {
        return ChoiceType::class;
    }
}
