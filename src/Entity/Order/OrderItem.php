<?php

declare(strict_types=1);

namespace App\Entity\Order;

use App\Entity\BilletiqueCardInterface;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\OrderItem as BaseOrderItem;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_order_item")
 */
class OrderItem extends BaseOrderItem
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BilletiqueCardInterface", )
     * @ORM\JoinColumn(name="billetique_card_id", nullable="true")
     */
    protected ?BilletiqueCardInterface $billetiqueCard = null;

    public function getBilletiqueCard(): ?BilletiqueCardInterface
    {
        return $this->billetiqueCard;
    }

    public function setBilletiqueCard(?BilletiqueCardInterface $billetiqueCard): void
    {
        $this->billetiqueCard = $billetiqueCard;
    }
}
