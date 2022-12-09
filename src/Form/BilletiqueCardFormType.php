<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class BilletiqueCardFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'app.form.billetique_card.name',
            ])
            ->add('customer', CustomerAutocompleteChoiceType::class, [
                'label' => 'sylius.ui.customer',
            ])
        ;
    }
}
