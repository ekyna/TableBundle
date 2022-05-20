<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Extension\AbstractColumnTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Column\BooleanType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use function Symfony\Component\Translation\t;

/**
 * Class BooleanColumnTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class BooleanColumnTypeExtension extends AbstractColumnTypeExtension
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $normalizer = function (Options $options, $value) {
            if ($value instanceof TranslatableInterface) {
                return $value->trans($this->translator);
            }

            return $value;
        };

        $resolver
            ->setDefaults([
                'true_label'  => t('value.yes', [], 'EkynaTable'),
                'false_label' => t('value.no', [], 'EkynaTable'),
            ])
            ->addAllowedTypes('true_label', TranslatableInterface::class)
            ->addAllowedTypes('false_label', TranslatableInterface::class)
            ->addNormalizer('true_label', $normalizer)
            ->addNormalizer('false_label', $normalizer);
    }

    public static function getExtendedTypes(): array
    {
        return [BooleanType::class];
    }
}
