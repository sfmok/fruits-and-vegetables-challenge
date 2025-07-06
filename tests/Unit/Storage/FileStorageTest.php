<?php

declare(strict_types=1);

namespace App\Tests\Unit\Storage;

use App\Entity\Fruit;
use App\Entity\Vegetable;
use App\Enum\ProduceType;
use App\Factory\ProduceFactory;
use App\Storage\FileStorage;
use PHPUnit\Framework\TestCase;

class FileStorageTest extends TestCase
{
    private FileStorage $storage;
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/produces_test_' . uniqid();
        $this->storage = new FileStorage(new ProduceFactory(), $this->tempDir);
    }

    protected function tearDown(): void
    {
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->tempDir);
        }
    }

    public function testStoreAndFindAll(): void
    {
        $fruit = (new Fruit())
            ->setId(1)
            ->setName('Apple')
            ->setQuantityInGrams(150.0);

        $this->storage->store(ProduceType::Fruit, 1, $fruit);

        $result = $this->storage->findAll(ProduceType::Fruit);

        $this->assertCount(1, $result);
        $this->assertInstanceOf(Fruit::class, $result[0]);
        $this->assertEquals(1, $result[0]->getId());
        $this->assertEquals('Apple', $result[0]->getName());
        $this->assertEquals(150.0, $result[0]->getQuantityInGrams());
    }

    public function testStoreDifferentProduceTypes(): void
    {
        $fruit = (new Fruit())->setId(1)->setName('Apple')->setQuantityInGrams(150.0);
        $vegetable = (new Vegetable())->setId(1)->setName('Carrot')->setQuantityInGrams(100.0);

        $this->storage->store(ProduceType::Fruit, 1, $fruit);
        $this->storage->store(ProduceType::Vegetable, 1, $vegetable);

        $fruits = $this->storage->findAll(ProduceType::Fruit);
        $vegetables = $this->storage->findAll(ProduceType::Vegetable);

        $this->assertCount(1, $fruits);
        $this->assertCount(1, $vegetables);
        $this->assertEquals('Apple', $fruits[0]->getName());
        $this->assertEquals('Carrot', $vegetables[0]->getName());
    }

    public function testRemoveExistingProduce(): void
    {
        $fruit = (new Fruit())->setId(1)->setName('Apple')->setQuantityInGrams(150.0);
        $this->storage->store(ProduceType::Fruit, 1, $fruit);

        $result = $this->storage->remove(ProduceType::Fruit, 1);

        $this->assertTrue($result);
        
        $items = $this->storage->findAll(ProduceType::Fruit);
        $this->assertCount(0, $items);
    }

    public function testRemoveNonExistingProduce(): void
    {
        $result = $this->storage->remove(ProduceType::Fruit, 999);

        $this->assertFalse($result);
    }

    public function testRemoveFromEmptyStorage(): void
    {
        $result = $this->storage->remove(ProduceType::Fruit, 1);

        $this->assertFalse($result);
    }

    public function testUpdateExistingProduce(): void
    {
        $fruit1 = (new Fruit())->setId(1)->setName('Apple')->setQuantityInGrams(150.0);
        $fruit2 = (new Fruit())->setId(1)->setName('Apple Updated')->setQuantityInGrams(200.0);

        $this->storage->store(ProduceType::Fruit, 1, $fruit1);
        $this->storage->store(ProduceType::Fruit, 1, $fruit2);

        $result = $this->storage->findAll(ProduceType::Fruit);

        $this->assertCount(1, $result);
        $this->assertEquals('Apple Updated', $result[0]->getName());
        $this->assertEquals(200.0, $result[0]->getQuantityInGrams());
    }

    public function testFindAllReturnsEmptyArrayForNewType(): void
    {
        $result = $this->storage->findAll(ProduceType::Vegetable);

        $this->assertIsArray($result);
        $this->assertCount(0, $result);
    }
} 