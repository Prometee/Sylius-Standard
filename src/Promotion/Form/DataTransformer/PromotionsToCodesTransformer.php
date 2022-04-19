<?php

declare(strict_types=1);

namespace App\Promotion\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Webmozart\Assert\Assert;

final class PromotionsToCodesTransformer implements DataTransformerInterface
{
    public function __construct(private PromotionRepositoryInterface $promotionRepository)
    {
    }

    public function transform($value): Collection
    {
        Assert::nullOrIsArray($value);

        if (empty($value)) {
            return new ArrayCollection();
        }

        return new ArrayCollection($this->promotionRepository->findBy(['code' => $value]));
    }

    public function reverseTransform($value): array
    {
        Assert::isInstanceOf($value, Collection::class);

        $promotionCodes = [];

        foreach ($value as $promotion) {
            Assert::isInstanceOf($promotion, PromotionInterface::class);
            $promotionCodes[] = $promotion->getCode();
        }

        return $promotionCodes;
    }
}
