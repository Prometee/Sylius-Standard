<?php

declare(strict_types=1);

namespace App\Promotion\Checker\Rule;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

class IsSubscribedToNewsletterRuleChecker implements RuleCheckerInterface
{
    /**
     * @param bool[] $configuration
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

        return $customer->isSubscribedToNewsletter() === $configuration['is_subscribed'];
    }
}
