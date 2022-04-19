<?php

declare(strict_types=1);

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

final class RangeAttributeConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('min', IntegerType::class, [
                'label' => 'app.form.range_attribute_type.min_label',
            ])
            ->add('max', IntegerType::class, [
                'label' => 'app.form.range_attribute_type.max_label',
            ])
            ->add('unit', ChoiceType::class, [
                'label' => 'app.form.range_attribute_type.unit_label',
                'choices' => [
                    'app.form.range_attribute_type.unit.meter' => 'm',
                    'app.form.range_attribute_type.unit.second' => 's',
                    'app.form.range_attribute_type.unit.mole' => 'mole',
                    'app.form.range_attribute_type.unit.ampere' => 'A',
                    'app.form.range_attribute_type.unit.kelvin' => 'K',
                    'app.form.range_attribute_type.unit.candela' => 'cd',
                    'app.form.range_attribute_type.unit.kilogram' => 'kg',
                ],
            ])
        ;
    }
}
