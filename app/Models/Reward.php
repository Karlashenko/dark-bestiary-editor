<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Reward extends Model implements Presentable
{
    public const TYPE_ITEMS            = 'ItemsReward';
    public const TYPE_ATTRIBUTE_POINTS = 'AttributePointsReward';
    public const TYPE_CURRENCIES       = 'CurrenciesReward';
    public const TYPE_TALENT_POINTS    = 'TalentPointsReward';
    public const TYPE_RANDOM_SKILLS    = 'RandomSkillsUnlockReward';
    public const TYPE_ATTRIBUTES       = 'AttributesReward';
    public const TYPE_PROPERTIES       = 'PropertiesReward';
    public const TYPE_LEVEL_UP         = 'LevelupReward';

    public const TYPES = [
        self::TYPE_ITEMS,
        self::TYPE_ATTRIBUTE_POINTS,
        self::TYPE_CURRENCIES,
        self::TYPE_TALENT_POINTS,
        self::TYPE_RANDOM_SKILLS,
        self::TYPE_LEVEL_UP,
        self::TYPE_ATTRIBUTES,
        self::TYPE_PROPERTIES,
    ];

    /**
     * @param Reward  $reward
     * @param Request $request
     *
     * @return Reward
     * @throws \Throwable
     */
    public static function populate(Reward $reward, Request $request): Reward
    {
        DB::transaction(function () use ($reward, $request) {
            $reward->type = (string) $request->type;
            $reward->label = (string) $request->label;
            $reward->level = (int) $request->level;
            $reward->random_skills_type = (string) $request->random_skills_type;
            $reward->random_skills_count = (int) $request->random_skills_count;
            $reward->talent_points = (int) $request->talent_points;
            $reward->attribute_points = (int) $request->attribute_points;
            $reward->save();

            $reward->rewards()->sync([]);
            foreach ((array) $request->get('rewards', []) as $data) {
                $reward->rewards()->attach((int) \array_get($data, 'id'));
            }

            $reward->attributes()->sync([]);
            foreach ((array) $request->get('attributes', []) as $data) {
                $reward->attributes()->attach(
                    (int) \array_get($data, 'id'),
                    ['amount' => (int) \array_get($data, 'pivot.amount')]
                );
            }

            $reward->properties()->sync([]);
            foreach ((array) $request->get('properties', []) as $data) {
                $reward->properties()->attach(
                    (int) \array_get($data, 'id'),
                    ['amount' => (int) \array_get($data, 'pivot.amount')]
                );
            }

            $reward->currencies()->sync([]);
            foreach ((array) $request->get('currencies', []) as $data) {
                $reward->currencies()->attach(
                    (int) \array_get($data, 'id'),
                    ['amount' => (int) \array_get($data, 'pivot.amount')]
                );
            }

            $reward->items()->sync([]);
            foreach ((array) $request->get('items', []) as $data) {
                $reward->items()->attach(
                    (int) \array_get($data, 'id'),
                    ['amount' => (int) \array_get($data, 'pivot.amount')]
                );
            }

            $reward->load($reward->with);
        });

        return $reward;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'rewards';
        $this->timestamps = false;
        $this->with = [
            'items',
            'attributes',
            'properties',
            'currencies',
            'rewards',
        ];
    }

    /**
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'rewards_items')
                    ->withPivot(['id', 'amount'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function rewards(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'rewards_rewards', 'parent_id', 'child_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, 'rewards_attributes')
                    ->withPivot(['id', 'amount'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        return $this->belongsToMany(Property::class, 'rewards_properties')
                    ->withPivot(['id', 'amount'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function currencies(): BelongsToMany
    {
        return $this->belongsToMany(Currency::class, 'rewards_currencies')
                    ->withPivot(['id', 'amount'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $presentation = [
            'Id'   => $this->id,
            'Type' => $this->type,
        ];

        if ($this->type === self::TYPE_RANDOM_SKILLS) {
            $presentation['Count'] = $this->random_skills_count;
        }

        if ($this->type === self::TYPE_TALENT_POINTS) {
            $presentation['Count'] = $this->talent_points;
        }

        if ($this->type === self::TYPE_ATTRIBUTE_POINTS) {
            $presentation['Count'] = $this->attribute_points;
        }

        if ($this->type === self::TYPE_LEVEL_UP) {
            $presentation['Level'] = $this->level;
            $presentation['Rewards'] = $this->rewards->map(function (Reward $reward) {
                return $reward->id;
            });
        }

        if ($this->type === self::TYPE_ITEMS) {
            $presentation['Items'] = $this->items->map(function (Item $item) {
                return [
                    'ItemId' => $item->id,
                    'Amount' => $item->pivot->amount,
                ];
            });
        }

        if ($this->type === self::TYPE_ATTRIBUTES) {
            $presentation['Attributes'] = $this->attributes->map(function (Attribute $attribute) {
                return [
                    'AttributeId' => $attribute->id,
                    'Amount'      => $attribute->pivot->amount,
                ];
            });
        }

        if ($this->type === self::TYPE_PROPERTIES) {
            $presentation['Properties'] = $this->properties->map(function (Property $property) {
                return [
                    'PropertyId' => $property->id,
                    'Amount'     => $property->pivot->amount,
                ];
            });
        }

        if ($this->type === self::TYPE_CURRENCIES) {
            $presentation['Currencies'] = $this->currencies->map(function (Currency $currency) {
                return [
                    'CurrencyId' => $currency->id,
                    'Amount'     => $currency->pivot->amount,
                ];
            });
        }

        return $presentation;
    }
}
