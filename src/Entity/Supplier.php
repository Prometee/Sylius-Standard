<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Resource\Model\TimestampableTrait;

/**
 * @ORM\Entity()
 * @ORM\Table(name="app_supplier")
 */
class Supplier implements SupplierInterface
{
    use TimestampableTrait;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="name", type="string")
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="email", type="string")
     */
    protected ?string $email;

    /**
     * @ORM\Column(name="status", type="string")
     */
    protected ?string $status = SupplierInterface::STATUS_NEW;

    public function __toString(): string
    {
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
}
