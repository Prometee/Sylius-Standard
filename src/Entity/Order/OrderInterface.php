<?php

declare(strict_types=1);

namespace App\Entity\Order;

use Sylius\Component\Core\Model\OrderInterface as BaseOrderInterface;

interface OrderInterface extends BaseOrderInterface
{
    public function getIban(): ?string;

    public function setIban(?string $iban): void;
}
