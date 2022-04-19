<?php

declare(strict_types=1);

namespace App\Promotion\Form\Type\Rule;

use App\Promotion\Form\DataTransformer\PromotionsToCodesTransformer;
use App\Promotion\Form\Type\PromotionAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class NeverHasThosePromotionsAppliedConfigurationType extends AbstractType
{
    private PromotionsToCodesTransformer $promotionsToCodesTransformer;

    public function __construct(PromotionsToCodesTransformer $promotionsToCodesTransformer)
    {
        $this->promotionsToCodesTransformer = $promotionsToCodesTransformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('promotions', PromotionAutocompleteChoiceType::class, [
                'label' => 'app.form.promotion_rule.never_has_those_promotions_applied.promotions',
                'multiple' => true,
                'required' => true,
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ],
            ])
        ;

        $builder->get('promotions')->addModelTransformer($this->promotionsToCodesTransformer);
    }

    public function getBlockPrefix(): string
    {
        return 'app_promotion_rule_never_has_those_promotions_applied';
    }
}
