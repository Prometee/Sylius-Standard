<?php

declare(strict_types=1);

namespace App\Order\Processor;

use App\Entity\Order\OrderItem;
use App\Entity\Product\ProductVariant;
use Sylius\Component\Core\Factory\CartItemFactoryInterface;
use Sylius\Component\Core\Repository\ProductVariantRepositoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Order\Modifier\OrderModifierInterface;
use Webmozart\Assert\Assert;

final class AddCardToNonCardedOrderItemProcessor implements AddCardToNonCardedOrderItemProcessorInterface
{
    public function __construct(
        private CartItemFactoryInterface $orderItemFactory,
        private ProductVariantRepositoryInterface $productVariantRepository,
        private OrderItemQuantityModifierInterface $itemQuantityModifier,
        private OrderModifierInterface $orderModifier,
    ) {
    }

    public function process(OrderInterface $order): void
    {
        $needCards = [];
        $cards = [];

        /** @var OrderItem $item */
        foreach ($order->getItems() as $item) {
            if (null !== $item->getBilletiqueCard()) {
                continue;
            }

            /** @var ProductVariant $variant */
            $variant = $item->getVariant();
            Assert::notNull($variant);

            $product = $variant->getProduct();
            Assert::notNull($product);

            if ('card' === $product->getCode()) {
                $cards[] = $item;
            }

            // @todo inversé default booleans value 😉
            if (false === $variant->isStackable()) {
                continue;
            }

            $needCards[] = $item;
        }

        $nbCardToAdd = count($needCards)-count($cards);
        /** @var ProductVariant|null $variant */
        $variant = $this->productVariantRepository->findOneByCode('card'); // /!\ code variant et non produit
        Assert::notNull($variant);


        if ($nbCardToAdd < 0) {
            for ($i = 0; $i < abs($nbCardToAdd); $i++) {
                $orderItem = $cards[count($cards)-($i+1)];
                $this->orderModifier->removeFromOrder($order , $orderItem);
            }
        }

        for ($i = 0; $i < $nbCardToAdd; $i++) {
            /** @var \Sylius\Component\Core\Model\OrderItem $orderItem */
            $orderItem = $this->orderItemFactory->createNew();

            $orderItem->setVariant($variant);
            $this->itemQuantityModifier->modify($orderItem, 1);
            $this->orderModifier->addToOrder($order , $orderItem);
        }
    }
}
