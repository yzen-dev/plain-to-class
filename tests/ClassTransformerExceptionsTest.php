<?php
declare(strict_types=1);

namespace Tests;

use ClassTransformer\Exceptions\ClassNotFoundException;
use Tests\DTO\BasketDTO;
use Tests\DTO\FakeClassDTO;
use Tests\DTO\ProductDTO;
use Tests\DTO\PurchaseDTO;
use Tests\DTO\UserDTO;
use PHPUnit\Framework\TestCase;
use ClassTransformer\ClassTransformer;

/**
 * Class ClassTransformerExceptionsTest
 * @package Tests
 */
class ClassTransformerExceptionsTest extends TestCase
{
    use FakerData;

    public function testClassNotFound(): void
    {
        $this->expectException(ClassNotFoundException::class);
        $data = [
            'fake' => ['exception']
        ];
        ClassTransformer::transform(FakeClassDTO::class, $data);
    }

}
