<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface as BaseOrderRepositoryInterface;

interface OrderRepositoryInterface extends BaseOrderRepositoryInterface
{
    /**
     * @param array|string[] $promotionCodes
     */
    public function countByCustomerAndPromotions(
        CustomerInterface $customer,
        array $promotionCodes
    ): int;
}
