<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;
use Sylius\Component\Core\Model\PromotionInterface;

class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    public function findByNamePart(string $name): array
    {
        /** @var PromotionInterface[] $promotions */
        $promotions = $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('o.id, o.code, o.name')
            ->from($this->getEntityName(), 'o')
            ->andWhere('o.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult();

        return $promotions;
    }
}
