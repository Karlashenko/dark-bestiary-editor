<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Archetype extends Model implements Presentable
{
    public const CURVE_TYPE_LINEAR = 'Linear';
    public const CURVE_TYPE_SLOW   = 'Slow';
    public const CURVE_TYPE_FAST   = 'Fast';

    public const CURVE_TYPES = [
        self::CURVE_TYPE_LINEAR,
        self::CURVE_TYPE_SLOW,
        self::CURVE_TYPE_FAST,
    ];

    /**
     * @param Archetype $achievement
     * @param \Request  $request
     *
     * @return Archetype
     * @throws \Throwable
     */
    public static function populate(Archetype $archetype, Request $request): self
    {
        DB::transaction(function () use ($archetype, $request) {
            $archetype->name = (string) $request->name;
            $archetype->save();

            $archetype->attributes()->sync([]);

            foreach ((new Collection((array) $request->get('attributes', [])))->unique('id') as $data) {

                $archetype->attributes()->attach(
                    (int) \array_get($data, 'id'),
                    [
                        'min'        => (int) \array_get($data, 'pivot.min'),
                        'max'        => (int) \array_get($data, 'pivot.max'),
                        'curve_type' => (string) \array_get($data, 'pivot.curve_type'),
                    ]);
            }

            $archetype->properties()->sync([]);

            foreach ((new Collection((array) $request->get('properties', [])))->unique('id') as $data) {
                $archetype->properties()->attach(
                    (int) \array_get($data, 'id'),
                    [
                        'min'        => (float) \array_get($data, 'pivot.min'),
                        'max'        => (float) \array_get($data, 'pivot.max'),
                        'curve_type' => (string) \array_get($data, 'pivot.curve_type'),
                    ]
                );
            }
        });

        $archetype->load($archetype->with);

        return $archetype;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'archetypes';
        $this->timestamps = false;
        $this->with = ['attributes', 'properties'];
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'archetypes_attributes')
                    ->withPivot(['min', 'max', 'curve_type'])
                    ->orderBy('index');
    }

    /**
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'archetypes_properties')
                    ->withPivot(['min', 'max', 'curve_type'])
                    ->orderBy('id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id' => $this->id,

            'Attributes' => $this->attributes()->get()->map(function (Attribute $attribute) {
                return [
                    'AttributeId' => $attribute->id,
                    'Min'         => $attribute->pivot->min,
                    'Max'         => $attribute->pivot->max,
                    'CurveType'   => $attribute->pivot->curve_type,
                ];
            }),

            'Properties' => $this->properties()->get()->map(function (Property $property) {
                return [
                    'PropertyId' => $property->id,
                    'Min'        => $property->pivot->min,
                    'Max'        => $property->pivot->max,
                    'CurveType'  => $property->pivot->curve_type,
                ];
            }),
        ];
    }

}
