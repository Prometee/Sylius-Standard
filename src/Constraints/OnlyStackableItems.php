<?php

declare(strict_types=1);

namespace App\Constraints;

use Symfony\Component\Validator\Constraint;

final class OnlyStackableItems extends Constraint
{
    public const HAS_ERROR = '403';

    public string $message = 'app.order_item.has_error';

    /**
     * /!\ Si pas de propertyPath alors l'erreur ne s'affichera pas et une bulle rouge sans texte s'affichera à la place
     */
    public string $propertyPath = 'quantity';

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
