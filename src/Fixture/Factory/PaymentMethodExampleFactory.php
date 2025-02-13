<?php

declare(strict_types=1);

namespace App\Fixture\Factory;

use Sylius\Bundle\CoreBundle\Fixture\Factory\PaymentMethodExampleFactory as BasePaymentMethodExampleFactory;
use Sylius\Bundle\PayumBundle\Model\GatewayConfigInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[AsDecorator('sylius.fixture.example_factory.payment_method')]
final class PaymentMethodExampleFactory extends BasePaymentMethodExampleFactory
{
    public function create(array $options = []): PaymentMethodInterface
    {
        $paymentMethod = parent::create($options);

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        $gatewayConfig->setUsePayum($options['usePayum']);

        return $paymentMethod;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefault('usPayum', false);
    }
}
