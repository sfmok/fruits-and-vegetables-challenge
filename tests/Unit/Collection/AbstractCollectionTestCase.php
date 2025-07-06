<?php

declare(strict_types=1);

namespace App\Tests\Unit\Collection;

use App\Factory\ProduceFactory;
use App\Storage\FileStorage;
use PHPUnit\Framework\TestCase;

abstract class AbstractCollectionTestCase extends TestCase
{
    protected FileStorage $storage;
    protected string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/produces_test_' . uniqid();
        $this->storage = new FileStorage(new ProduceFactory(), $this->tempDir);
    }

    protected function tearDown(): void
    {
        // Clean up test files
        if (is_dir($this->tempDir)) {
            $files = glob($this->tempDir . '/*');
            foreach ($files as $file) {
                unlink($file);
            }
            rmdir($this->tempDir);
        }
    }
} 