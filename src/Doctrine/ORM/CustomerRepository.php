<?php

declare(strict_types=1);

namespace App\Doctrine\ORM;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository implements CustomerRepositoryInterface
{
    public function findByEmailPart(string $email, int $limit = 25): array
    {
        return $this
            ->getEntityManager()
            ->createQueryBuilder()
            ->select('o.id', 'o.email')
            ->from($this->getEntityName(), 'o')
            ->andWhere('o.email LIKE :email')
            ->setParameter('email', '%' . $email . '%')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
            ;
    }
}
