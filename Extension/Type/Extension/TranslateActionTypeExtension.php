<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Extension\AbstractActionTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Action\ActionType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TranslateActionTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class TranslateActionTypeExtension extends AbstractActionTypeExtension
{
    use TranslateTrait;

    public function configureOptions(OptionsResolver $resolver): void
    {
        $this->configureTranslationOptions($resolver);
    }

    public static function getExtendedTypes(): array
    {
        return [ActionType::class];
    }
}
