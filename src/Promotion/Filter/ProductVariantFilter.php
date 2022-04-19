<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Promotion\Filter;

use Sylius\Component\Core\Promotion\Filter\FilterInterface;

final class ProductVariantFilter implements FilterInterface
{
    /**
     * @param array<array<string[]>> $configuration
     */
    public function filter(array $items, array $configuration): array
    {
        /** @var string[] $product_variants */
        $product_variants = $configuration['filters']['product_variants_filter']['product_variants'];
        if (empty($product_variants)) {
            return $items;
        }

        $filteredItems = [];
        foreach ($items as $item) {
            $variant = $item->getVariant();
            if (null === $variant) {
                continue;
            }
            if (in_array($variant->getCode(), $product_variants, true)) {
                $filteredItems[] = $item;
            }
        }

        return $filteredItems;
    }
}
