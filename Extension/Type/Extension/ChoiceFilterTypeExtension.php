<?php

declare(strict_types=1);

namespace Ekyna\Bundle\TableBundle\Extension\Type\Extension;

use Ekyna\Component\Table\Context\ActiveFilter;
use Ekyna\Component\Table\Extension\AbstractFilterTypeExtension;
use Ekyna\Component\Table\Extension\Core\Type\Filter\ChoiceType;
use Ekyna\Component\Table\Filter\FilterInterface;
use Ekyna\Component\Table\Util\FilterOperator;
use Ekyna\Component\Table\View\ActiveFilterView;
use Symfony\Component\Form\Extension\Core\Type as Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Class ChoiceFilterTypeExtension
 * @package Ekyna\Bundle\TableBundle\Extension\Type\Extension
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class ChoiceFilterTypeExtension extends AbstractFilterTypeExtension
{
    public function buildFilterForm(FormBuilderInterface $builder, FilterInterface $filter, array $options): bool
    {
        $builder
            ->add('operator', Form\ChoiceType::class, [
                'label'    => false,
                'required' => true,
                'choices'  => FilterOperator::getChoices([
                    FilterOperator::IN,
                    FilterOperator::NOT_IN,
                ]),
            ])
            ->add('value', Form\ChoiceType::class, [
                'label'                     => false,
                'multiple'                  => true,
                'required'                  => true,
                'choices'                   => $options['choices'],
                'choice_translation_domain' => $options['choice_translation_domain'] ?? $options['trans_domain'],
                'constraints'               => [
                    new NotBlank(),
                ],
            ]);

        return true;
    }

    public function buildActiveFilterView(
        ActiveFilterView $view,
        FilterInterface  $filter,
        ActiveFilter     $activeFilter,
        array            $options
    ): void {
        $view->vars['trans_domain'] = $options['choice_translation_domain'] ?? $options['trans_domain'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('choice_translation_domain', null)
            ->setAllowedTypes('choice_translation_domain', ['string', 'bool', 'null'])
            ->setAllowedValues('choice_translation_domain', function ($value) {
                if (true === $value) {
                    throw new InvalidOptionsException();
                }

                return true;
            });
    }

    public static function getExtendedTypes(): array
    {
        return [ChoiceType::class];
    }
}
