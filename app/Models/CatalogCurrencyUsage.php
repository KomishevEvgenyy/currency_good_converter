<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $currency_code
 * @property \DateTimeInterface $requested_at
 */
class CatalogCurrencyUsage extends Model
{
    public $timestamps = false;

    /**
     * @var string[]
     */
    protected $fillable = [
        'currency_code',
        'requested_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
        ];
    }
}
