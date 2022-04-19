<?php

declare(strict_types=1);

namespace App\AttributeType;

use Sylius\Component\Attribute\AttributeType\AttributeTypeInterface;
use Sylius\Component\Attribute\Model\AttributeValueInterface;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

final class RangeAttributeType implements AttributeTypeInterface
{
    public const TYPE = 'range';

    public function getStorageType(): string
    {
        return AttributeValueInterface::STORAGE_INTEGER;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    public function validate(AttributeValueInterface $attributeValue, ExecutionContextInterface $context, array $configuration): void
    {
        $value = $attributeValue->getValue();

        foreach ($this->getValidationErrors($context, $value, $configuration) as $error) {
            $context
                ->buildViolation($error->getMessage())
                ->atPath('value')
                ->addViolation()
            ;
        }
    }

    private function getValidationErrors(ExecutionContextInterface $context, ?float $value, array $configuration): ConstraintViolationListInterface
    {
        return $context->getValidator()->validate($value, [
            new Range(['min' => $configuration['min'], 'max' => $configuration['max']])
        ]);
    }
}
