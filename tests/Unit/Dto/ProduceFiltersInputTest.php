<?php

declare(strict_types=1);

namespace App\Tests\Unit\Dto;

use App\Enum\ProduceType;
use App\Enum\ProduceUnit;
use PHPUnit\Framework\TestCase;
use App\Dto\ProduceFiltersInput;
use Symfony\Component\Validator\Validation;

class ProduceFiltersInputTest extends TestCase
{
    public function testProduceFiltersInputWithAllParameters(): void
    {
        $filters = new ProduceFiltersInput(
            name: 'Apple',
            minQuantity: 100,
            maxQuantity: 500,
            type: ProduceType::Fruit->value,
            unit: ProduceUnit::Gram->value
        );

        $this->assertEquals('Apple', $filters->name);
        $this->assertEquals(100, $filters->minQuantity);
        $this->assertEquals(500, $filters->maxQuantity);
        $this->assertEquals(ProduceType::Fruit->value, $filters->type);
        $this->assertEquals(ProduceUnit::Gram->value, $filters->unit);
    }

    public function testToArrayMethod(): void
    {
        $filters = new ProduceFiltersInput(
            name: 'Carrot',
            minQuantity: 200,
            maxQuantity: 1000,
            type: ProduceType::Vegetable->value,
            unit: ProduceUnit::Gram->value
        );

        $expected = [
            'name' => 'Carrot',
            'min_quantity' => 200,
            'max_quantity' => 1000,
            'unit' => 'g',
        ];

        $this->assertEquals($expected, $filters->toArray());
    }

    public function testValidationWithValidData(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            name: 'Banana',
            minQuantity: 100,
            maxQuantity: 500,
            type: ProduceType::Fruit->value,
            unit: ProduceUnit::Gram->value
        );

        $violations = $validator->validate($filters);
        $this->assertCount(0, $violations);
    }

    public function testValidationWithInvalidMinQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            minQuantity: -10,
            maxQuantity: 500
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Minimum quantity must be positive', $violations[0]->getMessage());
    }

    public function testValidationWithInvalidMaxQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            minQuantity: 100,
            maxQuantity: -50
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(2, count($violations));
        $this->assertSame('Minimum quantity must be less than maximum quantity', $violations[0]->getMessage());
        $this->assertSame('Maximum quantity must be positive', $violations[1]->getMessage());
    }

    public function testValidationWithMinQuantityGreaterThanMaxQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            minQuantity: 500,
            maxQuantity: 100
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Minimum quantity must be less than maximum quantity', $violations[0]->getMessage());
    }

    public function testValidationWithMaxQuantityLessThanMinQuantity(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            minQuantity: 100,
            maxQuantity: 50
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Minimum quantity must be less than maximum quantity', $violations[0]->getMessage());
        $this->assertSame('Maximum quantity must be greater than minimum quantity', $violations[1]->getMessage());
    }

    public function testValidationWithInvalidType(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            type: 'invalid_type'
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Type must be either "fruit" or "vegetable"', $violations[0]->getMessage());
    }

    public function testValidationWithInvalidUnit(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            unit: 'invalid_unit'
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(1, count($violations));
        $this->assertSame('Unit must be either "g" or "kg"', $violations[0]->getMessage());
    }

    public function testValidationWithMultipleViolations(): void
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        $filters = new ProduceFiltersInput(
            minQuantity: -10,
            maxQuantity: -5,
            type: 'invalid_type',
            unit: 'invalid_unit'
        );

        $violations = $validator->validate($filters);
        $this->assertGreaterThanOrEqual(4, count($violations));
    }
} 