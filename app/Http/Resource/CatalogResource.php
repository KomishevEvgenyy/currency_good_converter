<?php

namespace App\Http\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Resource
 */
class CatalogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'price'     => $this->price,
            'rating'    => $this->rating,
            'thumbnail' => $this->thumbnail,
        ];
    }
}
