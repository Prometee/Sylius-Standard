<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository as BasePromotionRepository;

class PromotionRepository extends BasePromotionRepository implements PromotionRepositoryInterface
{
    public function findByNamePart(string $name): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('o.id, o.code, o.name')
            ->from($this->getEntityName(), 'o')
            ->andWhere('o.name LIKE :name')
            ->setParameter('name', '%' . $name . '%')
            ->getQuery()
            ->getResult()
            ;
    }
}
