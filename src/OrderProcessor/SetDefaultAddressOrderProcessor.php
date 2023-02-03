<?php

declare(strict_types=1);

namespace App\OrderProcessor;

use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class SetDefaultAddressOrderProcessor implements OrderProcessorInterface
{
    public function __construct(
        private StateMachineFactoryInterface $stateMachineFactory,
    ) {
    }

    public function process(BaseOrderInterface $order): void
    {
        Assert::isInstanceOf($order, OrderInterface::class);
        if (OrderCheckoutStates::STATE_CART !== $order->getCheckoutState()) {
            return;
        }

        /** @var CustomerInterface|null $customer */
        $customer = $order->getCustomer();
        if (null === $customer) {
            return;
        }

        $defaultAddress = $customer->getDefaultAddress();
        if (null === $defaultAddress) {
            return;
        }

        $order->setShippingAddress(clone $defaultAddress);
        $order->setBillingAddress(clone $defaultAddress);


        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        if ($stateMachine->can(OrderCheckoutTransitions::TRANSITION_ADDRESS)) {
            $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);
        }
    }
}
