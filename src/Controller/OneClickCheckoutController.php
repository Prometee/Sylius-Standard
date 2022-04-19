<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class OneClickCheckoutController
{
    public function __construct(
        private FactoryInterface $orderFactory,
        private ShopperContextInterface $shopperContext,
        private CartItemFactoryInterface $cartItemFactoryInterface,
        private RepositoryInterface $productVariantRepository,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
        private StateMachineFactoryInterface $stateMachineFactory,
        private EntityManagerInterface $orderManager,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function buyNowAction(int $variantId): Response {

        /** @var ChannelInterface $channel */
        $channel = $this->shopperContext->getChannel();

        /** @var CustomerInterface|null $customer */
        $customer = $this->shopperContext->getCustomer();
        if (null === $customer) {
            throw new AccessDeniedHttpException('Customer not found, please log in before using one click checkout !');
        }

        $baseCurrency = $channel->getBaseCurrency();
        Assert::notNull($baseCurrency);

        /** @var ProductVariantInterface|null $variant */
        $variant = $this->productVariantRepository->find($variantId);
        if (null === $variant) {
            throw new NotFoundHttpException(sprintf('Product variant with id "%s" does not exist !', $variantId));
        }

        $order = $this->createNewOrder($channel, $baseCurrency);
        $this->setCustomerAndAddress($order, $customer);
        $this->addProductVariant($order, $variant);

        $this->applyStateMachine($order);

        $this->orderManager->persist($order);
        $this->orderManager->flush();

        return new RedirectResponse(
            $this->urlGenerator->generate(
                'sylius_shop_account_order_show',
                ['number' => $order->getNumber()]
            )
        );
    }

    private function createNewOrder(ChannelInterface $channel, CurrencyInterface $baseCurrency): OrderInterface
    {
        /** @var OrderInterface $order */
        $order = $this->orderFactory->createNew();

        $order->setChannel($channel);
        $order->setCurrencyCode($baseCurrency->getCode());
        $order->setLocaleCode($this->shopperContext->getLocaleCode());
        return $order;
    }

    private function addProductVariant(OrderInterface $order, ProductVariantInterface $variant): void
    {
        $orderItem = $this->cartItemFactoryInterface->createForCart($order);
        $orderItem->setVariant($variant);
        $this->orderItemQuantityModifier->modify($orderItem, 1);
    }

    private function setCustomerAndAddress(OrderInterface $order, CustomerInterface $customer): void
    {
        $address = $customer->getDefaultAddress();
        if (null === $address) {
            throw new AccessDeniedHttpException('You should set a default address to use one click checkout !');
        }

        $order->setCustomer($customer);
        $order->setShippingAddress(clone $address);
        $order->setBillingAddress(clone $address);
    }

    private function applyStateMachine(OrderInterface $order): void
    {
        $stateMachine = $this->stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_ADDRESS);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_SHIPPING);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_SELECT_PAYMENT);
        $stateMachine->apply(OrderCheckoutTransitions::TRANSITION_COMPLETE);
    }
}
