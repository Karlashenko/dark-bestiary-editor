<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class AttributeModifier
 *
 * @package App\Models
 */
class AttributeModifier extends Model implements Presentable
{
    public const TYPES = [
        'Flat',
        'Fraction',
    ];

    /**
     * @inheritdocfraction_attribute_id
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'attribute_modifiers';
        $this->timestamps = false;
    }

    /**
     * @return MorphTo
     */
    public function modifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * @return BelongsTo
     */
    public function fractionAttribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'fraction_attribute_id')->withDefault();
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'AttributeId' => $this->attribute->id,
            'Amount'      => (float) $this->amount,
            'Type'        => $this->type,
            'AttributeFraction' => [
                'AttributeType' => (string) $this->fractionAttribute->type ?: 'None',
                'Fraction'      => (float) $this->fraction,
            ],
        ];
    }

}
