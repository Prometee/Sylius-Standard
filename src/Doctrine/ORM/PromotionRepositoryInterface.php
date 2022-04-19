<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Component\Core\Repository\PromotionRepositoryInterface as BasePromotionRepositoryInterface;

interface PromotionRepositoryInterface extends BasePromotionRepositoryInterface
{
    public function findByNamePart(string $name): array;
}
