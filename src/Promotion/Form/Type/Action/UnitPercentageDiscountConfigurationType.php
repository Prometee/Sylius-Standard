<?php

declare(strict_types=1);

namespace App\Promotion\Form\Type\Action;

use App\Promotion\Form\Type\PromotionFilterCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\Action\UnitPercentageDiscountConfigurationType as BaseUnitPercentageDiscountConfigurationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class UnitPercentageDiscountConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('filters', PromotionFilterCollectionType::class, [
                'required' => false,
                'currency' => $options['currency'],
            ])
        ;
    }

    public function getParent(): ?string
    {
        return BaseUnitPercentageDiscountConfigurationType::class;
    }
}
