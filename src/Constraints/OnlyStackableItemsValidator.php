<?php

declare(strict_types=1);

namespace App\Constraints;

use App\Entity\Product\ProductVariant;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Webmozart\Assert\Assert;

final class OnlyStackableItemsValidator extends ConstraintValidator
{
    public function __construct(private CartContextInterface $cartContext)
    {
    }
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof OnlyStackableItems) {
            throw new UnexpectedTypeException($constraint, OnlyStackableItems::class);
        }

        if (!$value instanceof OrderItemInterface) {
            throw new UnexpectedTypeException($value, OrderItemInterface::class);
        }

        /** @var ProductVariant|null $variantToAdd */
        $variantToAdd = $value->getVariant();
        Assert::notNull($variantToAdd);

        $order = $this->cartContext->getCart();

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            if ($value === $item) {
                continue;
            }

            /** @var ProductVariant|null $itproductVariantmVariant */
            $productVariant = $item->getVariant();
            Assert::notNull($productVariant);

            if ($variantToAdd->isStackable() && $productVariant->isStackable()) {
                continue;
            }

            if (!$variantToAdd->isStackable() && !$productVariant->isStackable()) {
                continue;
            }

            $this->context->buildViolation($constraint->message)
                ->setCode($constraint::HAS_ERROR)
                ->atPath($constraint->propertyPath)
                ->addViolation()
            ;

            break;
        }
    }
}
