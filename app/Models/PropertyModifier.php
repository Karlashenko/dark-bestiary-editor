<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class PropertyModifier
 *
 * @package App\Models
 */
class PropertyModifier extends Model implements Presentable
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'property_modifiers';
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
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * @return BelongsTo
     */
    public function fractionProperty(): BelongsTo
    {
        return $this->belongsTo(Property::class, 'fraction_property_id')->withDefault();
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
            'PropertyId'           => $this->property->id,
            'Amount'               => (float) $this->amount,
            'Formula'              => (string) $this->formula,
            'Type'                 => $this->type,
            'MaxAttributeFraction' => (float) $this->max_attribute_fraction,

            'PropertyFraction' => [
                'PropertyType' => (string) $this->fractionProperty->type ?: 'None',
                'Fraction'     => (float) $this->fraction,
            ],
            'AttributeFraction' => [
                'AttributeType' => (string) $this->fractionAttribute->type ?: 'None',
                'Fraction'      => (float) $this->fraction,
            ],
        ];
    }

}
