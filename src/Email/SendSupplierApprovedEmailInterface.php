<?php

declare(strict_types=1);

namespace App\Email;

use App\Entity\SupplierInterface;

interface SendSupplierApprovedEmailInterface
{
    public function send(SupplierInterface $supplier): void;
}
