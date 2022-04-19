<?php

declare(strict_types=1);

namespace App\Promotion\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class IsSubscribedToNewsletterConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('is_subscribed', CheckboxType::class, [
                'label' => 'app.form.promotion_rule.is_subscribed_to_newsletter.is_subscribed',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'app_promotion_rule_is_subscribed_to_newsletter';
    }
}
