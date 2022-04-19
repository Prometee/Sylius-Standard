<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\OrderRepository as BaseOrderRepository;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\OrderPaymentStates;
use Sylius\Component\Order\Model\OrderInterface;

final class OrderRepository extends BaseOrderRepository implements OrderRepositoryInterface
{
    public function countByCustomerAndPromotions(
        CustomerInterface $customer,
        array $promotionCodes
    ): int {
        $states = [OrderInterface::STATE_FULFILLED];
        $paymentStates = [
            OrderPaymentStates::STATE_PAID,
            OrderPaymentStates::STATE_PARTIALLY_PAID,
        ];

        /** @var string $count */
        $count = $this->createQueryBuilder('o')
            ->select('COUNT(o.id)')
            ->innerJoin('o.promotions', 'p')
            ->andWhere('o.customer = :customer')
            ->andWhere('p.code IN (:promotionCodes)')
            ->andWhere('o.state IN (:states)')
            ->andWhere('o.paymentState IN (:payment_states)')
            ->setParameter('customer', $customer)
            ->setParameter('promotionCodes', $promotionCodes)
            ->setParameter('states', $states)
            ->setParameter('payment_states', $paymentStates)
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return (int) $count;
    }
}
