<?php

declare(strict_types=1);

namespace App\Fixture\Factory;

use App\Entity\SupplierInterface;
use Faker\Factory;
use Faker\Generator;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Component\Resource\Factory\FactoryInterface as SyliusFactoryInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SupplierExampleFactory extends AbstractExampleFactory
{
    private Generator $faker;

    private OptionsResolver $optionsResolver;

    public function __construct(
        private SyliusFactoryInterface $supplierFactory,
        private StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->faker = Factory::create();
        $this->optionsResolver = new OptionsResolver();

        $this->configureOptions($this->optionsResolver);
    }

    /**
     * @param string[] $options
     */
    public function create(array $options = []): SupplierInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var SupplierInterface $supplier */
        $supplier = $this->supplierFactory->createNew();
        $supplier->setName($options['name']);
        $supplier->setEmail($options['email']);

        $this->applySupplierTransition($supplier, $options['status'] ?: $this->getRandomStatus());

        return $supplier;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('name', fn (Options $options): string => $this->faker->company())
            ->setDefault('email', fn (Options $options): string => $this->faker->email())
            ->setDefault('status', null)
        ;
    }

    private function getRandomStatus(): string
    {
        $statuses = [SupplierInterface::STATUS_NEW, SupplierInterface::STATUS_ACCEPTED, SupplierInterface::STATUS_REJECTED];

        return $statuses[random_int(0, 2)];
    }

    private function applySupplierTransition(SupplierInterface $supplier, string $targetState): void
    {
        /** @var StateMachineInterface $stateMachine */
        $stateMachine = $this->stateMachineFactory->get($supplier, 'app_supplier');
        $transition = $stateMachine->getTransitionToState($targetState);

        if (null !== $transition) {
            $stateMachine->apply($transition);
        }
    }
}
