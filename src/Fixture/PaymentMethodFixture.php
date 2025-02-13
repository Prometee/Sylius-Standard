<?php

declare(strict_types=1);

namespace App\Fixture;

use Doctrine\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\PaymentMethodFixture as BasePaymentMethodFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsDecorator('sylius.fixture.payment_method')]
final class PaymentMethodFixture extends BasePaymentMethodFixture
{
    public function __construct(
        #[Autowire(service: 'sylius.manager.payment_method')]
        ObjectManager $objectManager,
        #[Autowire(service: 'sylius.fixture.example_factory.payment_method')]
        ExampleFactoryInterface $exampleFactory,
    ) {
        parent::__construct($objectManager, $exampleFactory);
    }
    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        parent::configureResourceNode($resourceNode);

        $resourceNode
            ->children()
                ->booleanNode('usePayum')->defaultTrue()->end();
    }
}
