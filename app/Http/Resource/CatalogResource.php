<?php

namespace App\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read int $id
 * @property-read string $title
 * @property-read float $price
 * @property-read float $rating
 * @property-read string $thumbnail
 */
class CatalogResource extends JsonResource
{
    /**
     * @return array{id: int, title: string, price: float, rating: float, thumbnail: string}
     */
    public function toArray(Request $request): array
    {
        /** @var object{id: int, title: string, price: float, rating: float, thumbnail: string}&\stdClass $product */
        $product = $this->resource;

        return [
            'id' => $product->id,
            'title' => $product->title,
            'price' => $product->price,
            'rating' => $product->rating,
            'thumbnail' => $product->thumbnail,
        ];
    }
}
