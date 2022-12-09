<?php

declare(strict_types=1);

namespace App\Order\Modifier;

use App\Entity\Product\ProductVariant;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface as BaseOrderItemInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Webmozart\Assert\Assert;

final class CustomOrderModifier implements OrderModifierInterface
{
    public function __construct(
        private OrderProcessorInterface $orderProcessor,
        private OrderItemQuantityModifierInterface $orderItemQuantityModifier,
    ) {
    }

    public function addToOrder(OrderInterface $cart, BaseOrderItemInterface $cartItem): void
    {
        $this->resolveOrderItem($cart, $cartItem);

        $this->orderProcessor->process($cart);
    }

    public function removeFromOrder(OrderInterface $cart, BaseOrderItemInterface $item): void
    {
        $cart->removeItem($item);
        $this->orderProcessor->process($cart);
    }

    private function resolveOrderItem(OrderInterface $cart, BaseOrderItemInterface $item): void
    {
        foreach ($cart->getItems() as $existingItem) {
            if ($this->orderItemIsEqualToNewOrderItem($item, $existingItem)) {
                $this->orderItemQuantityModifier->modify(
                    $existingItem,
                    $existingItem->getQuantity() + $item->getQuantity(),
                );

                return;
            }
        }

        $cart->addItem($item);
    }

    private function orderItemIsEqualToNewOrderItem(BaseOrderItemInterface $item, OrderItemInterface $existingItem): bool
    {
        // Si le produit de l'Order Item est une souscription alors pas d'ajout de quantité
        /** @var ProductVariant|null $productVariant */
        $productVariant = $existingItem->getVariant();
        Assert::notNull($productVariant);

        $product = $productVariant->getProduct();
        Assert::notNull($product);
        if ('card' === $product->getCode()) {
            return false;
        }

        if ($productVariant->isStackable()) {
            return false;
        }

        return $item->equals($existingItem);
    }
}
