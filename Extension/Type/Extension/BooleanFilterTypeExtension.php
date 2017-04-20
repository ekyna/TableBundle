<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Extension\AbstractFilterTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Filter\BooleanType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BooleanFilterTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BooleanFilterTypeExtension extends AbstractFilterTypeExtension
{
    private TranslatorInterface $translator;


    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('choices', [
            $this->translator->trans('value.no', [], 'EkynaTable')  => false,
            $this->translator->trans('value.yes', [], 'EkynaTable') => true,
        ]);
    }

    public static function getExtendedTypes(): array
    {
        return [BooleanType::class];
    }
}
