<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface as BasePromotionRepositoryInterface;

interface PromotionRepositoryInterface extends BasePromotionRepositoryInterface
{
    /**
     * @return PromotionInterface[]
     */
    public function findByNamePart(string $name): array;
}
