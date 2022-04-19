<?php

declare(strict_types=1);

namespace App\Promotion\Checker\Rule;

use App\Doctrine\ORM\OrderRepositoryInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class NeverHasThosePromotionsAppliedRuleChecker implements RuleCheckerInterface
{
    public function __construct(private OrderRepositoryInterface $orderRepository)
    {
    }

    /**
     * @param array<string[]> $configuration
     */
    public function isEligible(PromotionSubjectInterface $subject, array $configuration): bool
    {
        if (false === $subject instanceof OrderInterface) {
            throw new UnsupportedTypeException($subject, OrderInterface::class);
        }

        /** @var CustomerInterface|null $customer */
        $customer = $subject->getCustomer();
        if (null === $customer) {
            return false;
        }

        $promotions = $configuration['promotions'];

        return $this->orderRepository->countByCustomerAndPromotions($customer, $promotions) === 0;
    }
}
