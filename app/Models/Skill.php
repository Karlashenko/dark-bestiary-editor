<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\SkillRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * Class Skill
 *
 * @package App\Models
 */
class Skill extends Model implements Presentable
{
    public const FLAGS = [
        'Passive',
        'DualWield',
        'MeleeWeapon',
        'RangedWeapon',
        'MagicWeapon',
        'Magic',
        'Movement',
        'CheckLineOfSight',
        'FixedRange',
        'Delayed',
        'EndTurn',
        'Monster',
        'Item',
        'Talent',
    ];

    public const TARGET_TYPES = [
        'None',
        'Unit',
        'OtherUnit',
        'EnemyUnit',
        'AllyUnit',
        'Point',
        'Unoccupied',
        'Corpse',
    ];

    public const TYPES = [
        'Weapon',
        'Common',
    ];

    public const RESOURCE_TYPES = [
        'ActionPoint',
        'Rage',
    ];

    public const SHAPES = [
        'Circle',
        'Cross',
        'Line',
        'Cleave',
        'Cone2',
        'Cone3',
        'Cone5',
    ];

    /**
     * @param Skill        $skill
     * @param SkillRequest $request
     *
     * @return Skill
     */
    public static function populate(Skill $skill, SkillRequest $request): self
    {
        $skill->label = (string) $request->label;
        $skill->prefix = (string) $request->prefix;
        $skill->name_i18n_id = $request->name_i18n_id;
        $skill->description_i18n_id = $request->description_i18n_id;
        $skill->lore_i18n_id = $request->lore_i18n_id;
        $skill->commentary = (string) $request->commentary;
        $skill->flags = (new Collection((array) $request->flags))->filter(function (string $flag) {return \in_array($flag, self::FLAGS);})->values();
        $skill->type = $request->type ?: self::TYPES[0];
        $skill->target_type = $request->target_type ?: self::TARGET_TYPES[0];
        $skill->category_id = $request->category_id;
        $skill->effect_id = $request->effect_id;
        $skill->rarity_id = $request->rarity_id;
        $skill->behaviour_id = $request->behaviour_id;
        $skill->range_shape = (string) $request->range_shape ?: self::SHAPES[0];
        $skill->required_item_category_id = $request->required_item_category_id;
        $skill->required_level = (int) $request->required_level;
        $skill->animation = (string) $request->animation;
        $skill->icon = (string) $request->icon;
        $skill->cooldown = (int) $request->cooldown;
        $skill->aoe = (int) $request->aoe;
        $skill->aoe_shape = (string) $request->aoe_shape ?: self::SHAPES[0];
        $skill->range_min = (int) $request->range_min;
        $skill->range_max = (int) $request->range_max;
        $skill->is_enabled = (bool) $request->is_enabled;
        $skill->save();

        $skill->costs()->delete();

        foreach ((array) $request->costs as $cost) {
            $skillCost = new SkillCost();
            $skillCost->skill_id = $skill->id;
            $skillCost->resource_type = \array_get($cost, 'resource_type');
            $skillCost->amount = (int) \array_get($cost, 'amount');
            $skillCost->save();
        }

        $skill->prices()->delete();

        foreach ((array) $request->get('prices', []) as $data) {
            $price = new Price();
            $price->priceable_id = $skill->id;
            $price->priceable_type = \get_class($skill);
            $price->currency_id = \array_get($data, 'currency_id');
            $price->amount = (int) \array_get($data, 'amount');
            $price->save();
        }

        $skill->skills()->sync([]);

        foreach ((array) $request->get('skills', []) as $data) {
            $skill->skills()->attach((int) \array_get($data, 'id'));
        }

        $skill->load($skill->with);

        return $skill;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'skills';
        $this->timestamps = false;
        $this->with = [
            'costs',
            'prices',
            'nameI18n',
            'effect',
            'rarity',
            'descriptionI18n',
            'loreI18n',
            'requiredItemCategory',
            'category',
            'skills',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['flags'] = 'array';
        $casts['effect_data'] = 'array';

        return $casts;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeEnabled(Builder $builder): Builder
    {
        return $builder->where('is_enabled', true);
    }

    /**
     * @return BelongsTo
     */
    public function requiredItemCategory() : BelongsTo
    {
        return $this->belongsTo(ItemCategory::class, 'required_item_category_id')->withDefault(['name' => 'None']);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'skills_skills', 'parent_id', 'child_id')
                    ->without('skills')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function sets(): BelongsToMany
    {
        return $this->belongsToMany(SkillSet::class, 'skill_sets_skills')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsTo
     */
    public function effect(): BelongsTo
    {
        return $this->belongsTo(Effect::class, 'effect_id');
    }

    /**
     * @return BelongsTo
     */
    public function rarity(): BelongsTo
    {
        return $this->belongsTo(ItemRarity::class, 'rarity_id')->withDefault();
    }

    /**
     * @return MorphMany
     */
    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable')->orderBy('id');
    }

    /**
     * @return HasMany
     */
    public function costs(): HasMany
    {
        return $this->hasMany(SkillCost::class, 'skill_id')
                    ->orderBy('id');
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')
                    ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SkillCategory::class, 'category_id')
                    ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function loreI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'lore_i18n_id')
                    ->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function descriptionI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')
                    ->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $costs = $this->costs->map(function (SkillCost $skillCost) { return [$skillCost->resource_type => $skillCost->amount]; })
                             ->collapse()
                             ->toArray();

        return [
            'Id'                     => $this->id,
            'NameKey'                => $this->nameI18n->key,
            'DescriptionKey'         => $this->descriptionI18n->key,
            'LoreKey'                => $this->loreI18n->key,
            'Flags'                  => \implode(', ', $this->flags ?: ['None']),
            'Type'                   => $this->type,
            'TargetType'             => $this->target_type,
            'Animation'              => $this->animation,
            'Icon'                   => $this->icon,
            'Cooldown'               => $this->cooldown,
            'Price'                  => $this->prices->present(),
            'AOE'                    => $this->aoe,
            'AOEShape'               => (string) $this->aoe_shape,
            'RangeMin'               => $this->range_min,
            'RangeMax'               => $this->range_max,
            'RangeShape'             => $this->range_shape,
            'RequiredItemCategoryId' => (int) $this->required_item_category_id,
            'RequiredLevel'          => (int) $this->required_level,
            'CategoryId'             => (int) $this->category_id,
            'EffectId'               => (int) $this->effect_id,
            'BehaviourId'            => (int) $this->behaviour_id,
            'RarityId'               => (int) $this->rarity->id,
            'Sets'                   => $this->sets->pluck('id')->toArray(),
            'Skills'                 => $this->skills->pluck('id')->toArray(),
            'ResourcesCosts'         => (object) $costs,
        ];
    }
}
