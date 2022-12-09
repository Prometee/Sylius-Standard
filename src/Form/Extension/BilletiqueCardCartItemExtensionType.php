<?php

declare(strict_types=1);

namespace App\Form\Extension;

use App\Entity\BilletiqueCard;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class BilletiqueCardCartItemExtensionType extends AbstractTypeExtension
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('billetique_card', EntityType::class, [
            'class' => BilletiqueCard::class,
            'choice_label' => 'name',
            'required' => false,
            'placeholder' => '-- Sélectionnez une carte -- ',
        ]);
    }

    public static function getExtendedTypes(): iterable
    {
        return [CartItemType::class];
    }
}
