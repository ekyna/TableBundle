<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Trait TranslateTrait
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
trait TranslateTrait
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function configureTranslationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->addAllowedTypes('label', TranslatableInterface::class)
            ->setDefault('trans_domain', false)
            ->setAllowedTypes('trans_domain', ['null', 'bool', 'string'])
            ->setAllowedValues('trans_domain', function ($value) {
                if (true === $value) {
                    throw new InvalidOptionsException();
                }

                return true;
            })
            ->addNormalizer('label', function (Options $options, $value) {
                if ($value instanceof TranslatableInterface) {
                    return $value->trans($this->translator);
                }

                return $value;
            });
    }
}
