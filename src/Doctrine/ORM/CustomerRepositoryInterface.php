<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Component\Core\Repository\CustomerRepositoryInterface as BaseCustomerRepositoryInterface;

interface CustomerRepositoryInterface extends BaseCustomerRepositoryInterface
{
    public function findByEmailPart(string $email): array;
}
