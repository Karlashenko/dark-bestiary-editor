<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemModifier extends Model implements Presentable
{
    /**
     * @param ItemModifier $itemModifier
     * @param Request      $request
     *
     * @return ItemModifier
     * @throws \Throwable
     */
    public static function populate(ItemModifier $itemModifier, Request $request): self
    {
        DB::transaction(function () use ($itemModifier, $request) {
            $itemModifier->label = (string) $request->label;
            $itemModifier->suffix_i18n_id = $request->suffix_i18n_id;
            $itemModifier->rarity_id = $request->rarity_id;
            $itemModifier->is_suffix = (bool) $request->is_suffix;
            $itemModifier->is_weapon = (bool) $request->is_weapon;
            $itemModifier->is_base = (bool) $request->is_base;
            $itemModifier->required_item_level = (int) $request->required_item_level;
            $itemModifier->save();

            $itemModifier->attributes()->sync([]);

            foreach ((array) $request->get('attributes', []) as $data) {
                $itemModifier->attributes()->attach(
                    (int) \array_get($data, 'id'),
                    [
                        'min'        => \array_get($data, 'pivot.min'),
                        'max'        => \array_get($data, 'pivot.max'),
                        'fraction'   => \array_get($data, 'pivot.fraction'),
                        'curve_type' => \array_get($data, 'pivot.curve_type'),
                    ]
                );
            }

            $itemModifier->properties()->sync([]);

            foreach ((array) $request->get('properties', []) as $data) {
                $itemModifier->properties()->attach(
                    (int) \array_get($data, 'id'),
                    [
                        'min'        => \array_get($data, 'pivot.min'),
                        'max'        => \array_get($data, 'pivot.max'),
                        'fraction'   => \array_get($data, 'pivot.fraction'),
                        'curve_type' => \array_get($data, 'pivot.curve_type'),
                    ]
                );
            }

            $itemModifier->itemModifiers()->sync([]);

            foreach ((array) $request->get('item_modifiers', []) as $data) {
                $itemModifier->itemModifiers()->attach((int) \array_get($data, 'id'));
            }

            $itemModifier->categories()->sync([]);

            foreach ((array) $request->get('categories', []) as $category) {
                $id = (int) \array_get($category, 'id');

                if ($id === 0) {
                    continue;
                }

                $itemModifier->categories()->attach($id);
            }
        });

        $itemModifier->load($itemModifier->with);

        return $itemModifier;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'item_modifiers';
        $this->timestamps = false;
        $this->with = ['suffixI18n', 'properties', 'attributes', 'categories', 'rarity', 'itemModifiers'];
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['inline_stats'] = $this->itemModifiers->map(function (ItemModifier $itemModifier) {
            return $itemModifier->label;
        })->implode(' ');

        return $array;
    }

    /**
     * @return BelongsTo
     */
    public function suffixI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'suffix_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function rarity(): BelongsTo
    {
        return $this->belongsTo(ItemRarity::class, 'rarity_id')->withDefault();
    }

    public function itemModifiers(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'item_modifiers_item_modifiers', 'parent_id', 'child_id')
                    ->without('itemModifiers')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ItemCategory::class, 'item_modifiers_item_categories')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'item_modifiers_attributes')
                    ->withPivot(['id', 'min', 'max', 'fraction', 'curve_type'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'item_modifiers_properties')
                    ->withPivot(['id', 'min', 'max', 'fraction', 'curve_type'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'                => $this->id,
            'SuffixTextKey'     => $this->suffixI18n->key,
            'IsSuffix'          => $this->is_suffix,
            'IsWeapon'          => $this->is_weapon,
            'IsBase'            => $this->is_base,
            'RarityId'          => (int) $this->rarity_id,
            'RequiredItemLevel' => $this->required_item_level,
            'Categories'        => $this->categories->pluck('id'),
            'ItemModifiers'     => $this->itemModifiers->pluck('id'),

            'Attributes' => $this->attributes()->get()->map(function (Attribute $attribute) {
                return [
                    'AttributeId' => $attribute->id,
                    'Min'         => $attribute->pivot->min,
                    'Max'         => $attribute->pivot->max,
                    'Fraction'    => $attribute->pivot->fraction,
                    'CurveType'   => $attribute->pivot->curve_type,
                ];
            }),

            'Properties' => $this->properties()->get()->map(function (Property $property) {
                return [
                    'PropertyId' => $property->id,
                    'Min'        => $property->pivot->min,
                    'Max'        => $property->pivot->max,
                    'Fraction'   => $property->pivot->fraction,
                    'CurveType'  => $property->pivot->curve_type,
                ];
            }),
        ];
    }
}
