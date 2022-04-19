<?php

declare(strict_types=1);

namespace App\Entity;

use Stringable;
use Sylius\Component\Resource\Model\ResourceInterface;

interface SupplierInterface extends
    ResourceInterface,
    Stringable
{
    public const STATUS_NEW = 'new';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    public function getId(): ?int;

    public function getName(): ?string;

    public function setName(?string $name): void;

    public function getEmail(): ?string;

    public function setEmail(?string $email): void;
}
