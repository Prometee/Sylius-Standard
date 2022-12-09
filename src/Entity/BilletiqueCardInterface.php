<?php

declare(strict_types=1);

namespace App\Entity;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;

interface BilletiqueCardInterface extends ResourceInterface, TimestampableInterface
{
    public function getName(): ?string;
    public function setName(?string $name): void;

    public function getCustomer(): ?CustomerInterface;
    public function setCustomer(?CustomerInterface $customer): void;
}
