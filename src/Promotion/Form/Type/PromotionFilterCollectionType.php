<?php

declare(strict_types=1);

namespace App\Promotion\Form\Type;

use App\Promotion\Form\Type\Filter\ProductVariantsFilterConfigurationType;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionFilterCollectionType as BasePromotionFilterCollectionType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionFilterCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('product_variants_filter', ProductVariantsFilterConfigurationType::class, [
            'label' => false,
            'required' => false,
        ]);

        $builder->remove('products_filter');
    }

    public function getParent()
    {
        return BasePromotionFilterCollectionType::class;
    }
}
