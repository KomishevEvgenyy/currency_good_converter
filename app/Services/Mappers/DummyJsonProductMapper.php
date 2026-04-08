<?php

namespace App\Services\Mappers;

final class DummyJsonProductMapper
{
    /**
     * @return list<array{id: int, title: string, price: numeric, rating: numeric, thumbnail: string}>
     */
    public function mapProducts(mixed $payload): array
    {
        if (! is_array($payload) || ! array_key_exists('products', $payload) || ! is_array($payload['products'])) {
            throw new \RuntimeException('DummyJSON API returned invalid payload: [products] must be an array');
        }

        $products = [];

        foreach ($payload['products'] as $index => $product) {
            if (! is_array($product)) {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: expected object array"
                );
            }

            foreach (['id', 'title', 'price', 'rating', 'thumbnail'] as $requiredKey) {
                if (! array_key_exists($requiredKey, $product)) {
                    throw new \RuntimeException(
                        "DummyJSON API returned invalid product at index [{$index}]: missing key [{$requiredKey}]"
                    );
                }
            }

            if (! is_int($product['id'])) {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: [id] must be an integer"
                );
            }

            if (! is_string($product['title']) || trim($product['title']) === '') {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: [title] must be a non-empty string"
                );
            }

            if (! is_numeric($product['price'])) {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: [price] must be numeric"
                );
            }

            if (! is_numeric($product['rating'])) {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: [rating] must be numeric"
                );
            }

            if (! is_string($product['thumbnail']) || trim($product['thumbnail']) === '') {
                throw new \RuntimeException(
                    "DummyJSON API returned invalid product at index [{$index}]: [thumbnail] must be a non-empty string"
                );
            }

            $products[] = [
                'id' => $product['id'],
                'title' => $product['title'],
                'price' => $product['price'],
                'rating' => $product['rating'],
                'thumbnail' => $product['thumbnail'],
            ];
        }

        return $products;
    }
}
