<?php

namespace Tests\Unit;

use App\Services\Mappers\DummyJsonProductMapper;
use PHPUnit\Framework\TestCase;

class DummyJsonProductMapperTest extends TestCase
{
    public function test_it_maps_valid_products_payload(): void
    {
        $mapper = new DummyJsonProductMapper();

        $products = $mapper->mapProducts([
            'products' => [
                [
                    'id' => 1,
                    'title' => 'iPhone 5s',
                    'price' => 199.99,
                    'rating' => 2.83,
                    'thumbnail' => 'https://example.com/thumbnail.webp',
                ],
            ],
        ]);

        $this->assertCount(1, $products);
        $this->assertSame(1, $products[0]['id']);
        $this->assertSame('iPhone 5s', $products[0]['title']);
    }

    public function test_it_throws_when_products_is_missing(): void
    {
        $mapper = new DummyJsonProductMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DummyJSON API returned invalid payload: [products] must be an array');

        $mapper->mapProducts([]);
    }

    public function test_it_throws_when_product_row_is_not_array(): void
    {
        $mapper = new DummyJsonProductMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DummyJSON API returned invalid product at index [0]: expected object array');

        $mapper->mapProducts([
            'products' => ['invalid'],
        ]);
    }

    public function test_it_throws_when_required_key_is_missing(): void
    {
        $mapper = new DummyJsonProductMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DummyJSON API returned invalid product at index [0]: missing key [title]');

        $mapper->mapProducts([
            'products' => [
                [
                    'id' => 1,
                    'price' => 199.99,
                    'rating' => 2.83,
                    'thumbnail' => 'https://example.com/thumbnail.webp',
                ],
            ],
        ]);
    }

    public function test_it_throws_when_price_is_not_numeric(): void
    {
        $mapper = new DummyJsonProductMapper();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('DummyJSON API returned invalid product at index [0]: [price] must be numeric');

        $mapper->mapProducts([
            'products' => [
                [
                    'id' => 1,
                    'title' => 'iPhone 5s',
                    'price' => 'invalid',
                    'rating' => 2.83,
                    'thumbnail' => 'https://example.com/thumbnail.webp',
                ],
            ],
        ]);
    }
}
