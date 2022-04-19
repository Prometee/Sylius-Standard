<?php

declare(strict_types=1);

namespace App\Promotion\Form\Type\Filter;

use App\Promotion\Form\Type\ProductVariantAutocompleteChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductVariantsFilterConfigurationType extends AbstractType
{
    public function __construct(private DataTransformerInterface $productVariantsToCodesTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('product_variants', ProductVariantAutocompleteChoiceType::class, [
                'label' => 'app.form.promotion_filter.product_variants',
                'multiple' => true,
            ])
        ;

        $builder->get('product_variants')->addModelTransformer($this->productVariantsToCodesTransformer);
    }
}
