<?php

declare(strict_types=1);

namespace App\Checkout;

use Sylius\Bundle\CoreBundle\Checkout\CheckoutStateUrlGeneratorInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutStates;
use Symfony\Component\Routing\RequestContext;

final class CompletedCheckoutStateUrlGenerator implements CheckoutStateUrlGeneratorInterface
{
    public function __construct(
        private CheckoutStateUrlGeneratorInterface $decoratedCheckoutStateUrlGenerator,
    ) {
    }

    public function generateForOrderCheckoutState(OrderInterface $order, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH,): string
    {
        if (OrderCheckoutStates::STATE_COMPLETED === $order->getCheckoutState()) {
            $parameters += [
                'tokenValue' => $order->getTokenValue(),
            ];
        }

        return $this->decoratedCheckoutStateUrlGenerator->generateForOrderCheckoutState($order, $parameters, $referenceType);
    }

    public function generateForCart(array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->decoratedCheckoutStateUrlGenerator->generateForCart($parameters, $referenceType);
    }

    public function setContext(RequestContext $context): void
    {
        $this->decoratedCheckoutStateUrlGenerator->setContext($context);
    }

    public function getContext(): RequestContext
    {
        return $this->decoratedCheckoutStateUrlGenerator->getContext();
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        return $this->decoratedCheckoutStateUrlGenerator->generate($name, $parameters, $referenceType);
    }
}
