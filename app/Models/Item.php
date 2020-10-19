<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\ItemRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Class Item
 *
 * @package App\Models
 */
class Item extends Model implements Presentable
{
    public const FLAGS = [
        'Stackable',
        'Craftable',
        'Gambleable',
        'Dismantable',
        'HasRandomSuffix',
        'HasRandomSocketCount',
        'HasBlueprint',
        'Droppable',
        'UniqueEquipped',
        'QuestReward',
        'Illusory',
        'CampaignOnly',
        'VisionsOnly',
    ];

    public const SLOT_TYPES = [
        'None',
        'AnyHand',
        'MainHand',
        'OffHand',
        'Neck',
        'Ring',
        'Head',
        'Chest',
        'Hands',
        'Belt',
        'Feet',
        'Relic',
    ];

    /**
     * @param Item        $item
     * @param ItemRequest $request
     *
     * @return Item
     * @throws \Exception
     * @throws \Throwable
     */
    public static function populate(Item $item, ItemRequest $request): self
    {
        DB::transaction(function () use ($item, $request) {
            $item->name_i18n_id = $request->name_i18n_id;
            $item->book_text_i18n_id = $request->book_text_i18n_id;
            $item->consume_description_i18n_id = $request->consume_description_i18n_id;
            $item->passive_description_i18n_id = $request->passive_description_i18n_id;
            $item->lore_i18n_id = $request->lore_i18n_id;
            $item->rarity_id = $request->rarity_id;
            $item->type_id = $request->type_id;
            $item->set_id = $request->set_id;
            $item->skin_id = $request->skin_id;
            $item->suffix_id = $request->suffix_id;
            $item->consume_loot_id = $request->consume_loot_id;
            $item->consume_effect_id = $request->consume_effect_id;
            $item->consume_sound = (string) $request->consume_sound;
            $item->consume_cooldown = (int) $request->consume_cooldown;
            $item->learn_skill_id = $request->learn_skill_id;
            $item->unlock_skill_id = $request->unlock_skill_id;
            $item->unlock_relic_id = $request->unlock_relic_id;
            $item->unlock_scenario_id = $request->unlock_scenario_id;
            $item->enchantment_behaviour_id = $request->enchantment_behaviour_id;
            $item->enchantment_item_category_id = $request->enchantment_item_category_id;
            $item->comment = (string) $request->comment;
            $item->slot = (string) $request->slot;
            $item->icon = (string) $request->icon;
            $item->flags = (array) $request->flags;
            $item->currency_type = (string) $request->currency_type;
            $item->weapon_skill_1_id = $request->weapon_skill_1_id;
            $item->weapon_skill_2_id = $request->weapon_skill_2_id;
            $item->stack_size = (int) $request->stack_size;
            $item->socket_count = (int) $request->socket_count;
            $item->level = (int) $request->level;
            $item->required_level = (int) $request->required_level;
            $item->is_enabled = (bool) $request->is_enabled;
            $item->save();

            $item->attributeModifiers()->delete();

            foreach ((array) $request->get('attribute_modifiers', []) as $data) {
                $modifier = new AttributeModifier();
                $modifier->modifiable_id = $item->id;
                $modifier->modifiable_type = \get_class($item);
                $modifier->attribute_id = \array_get($data, 'attribute_id');
                $modifier->amount = (float) \array_get($data, 'amount');
                $modifier->type = (string) \array_get($data, 'type');
                $modifier->save();
            }

            $item->propertyModifiers()->delete();

            foreach ((array) $request->get('property_modifiers', []) as $data) {
                $modifier = new PropertyModifier();
                $modifier->modifiable_id = $item->id;
                $modifier->modifiable_type = \get_class($item);
                $modifier->property_id = \array_get($data, 'property_id');
                $modifier->amount = (float) \array_get($data, 'amount');
                $modifier->type = (string) \array_get($data, 'type');
                $modifier->save();
            }

            $item->prices()->delete();

            foreach ((array) $request->get('prices', []) as $data) {
                $price = new Price();
                $price->priceable_id = $item->id;
                $price->priceable_type = \get_class($item);
                $price->currency_id = \array_get($data, 'currency_id');
                $price->amount = (int) \array_get($data, 'amount');
                $price->save();
            }

            $item->attachments()->delete();

            foreach ((array) $request->get('attachments', []) as $data) {
                $attachment = new Attachment();
                $attachment->point = (string) \array_get($data, 'point');
                $attachment->prefab = (string) \array_get($data, 'prefab');
                $attachment->rotate = (bool) \array_get($data, 'rotate');
                $attachment->rotate_same_as_caster = (bool) \array_get($data, 'rotate_same_as_caster');
                $attachment->original_scale = false;
                $attachment->attachable_id = $item->id;
                $attachment->attachable_type = __CLASS__;
                $attachment->save();
            }

            $item->behaviours()->sync([]);

            foreach ((array) $request->get('behaviours', []) as $behaviour) {
                $id = (int) \array_get($behaviour, 'id');

                if ($id === 0) {
                    continue;
                }

                $item->behaviours()->attach($id);
            }

            $item->itemModifiers()->sync([]);

            foreach ((array) $request->get('item_modifiers', []) as $itemModifier) {
                $id = (int) \array_get($itemModifier, 'id');

                if ($id === 0) {
                    continue;
                }

                $item->itemModifiers()->attach($id, ['is_fixed' => (bool) \array_get($itemModifier, 'pivot.is_fixed')]);
            }
        });

        $item->load($item->with);

        return $item;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'items';

        $this->with = [
            'nameI18n',
            'loreI18n',
            'consumeDescriptionI18n',
            'type',
            'rarity',
            'suffix',
            'blueprintRecipe',
            'attributeModifiers',
            'propertyModifiers',
            'itemModifiers',
            'prices',
            'behaviours',
            'attachments',
            'weaponSkillA',
            'weaponSkillB',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['flags'] = 'array';
        return $casts;
    }

    public function isTwoHandedWeapon(): bool
    {
        $category = ItemCategory::whereType('TwoHandedWeapon')->first();

        if ($category === null) {
            return false;
        }

        return $category->types->contains(function (ItemType $type) { return $type->id === $this->type_id; });
    }

    public function isArmor(): bool
    {
        $category = ItemCategory::whereType('ChestArmor')->first();

        if ($category === null) {
            return false;
        }

        return $category->types->contains(function (ItemType $type) { return $type->id === $this->type_id; });
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
     * @return MorphMany
     */
    public function attributeModifiers(): MorphMany
    {
        return $this->morphMany(AttributeModifier::class, 'modifiable')->orderBy('id');
    }

    /**
     * @return MorphMany
     */
    public function propertyModifiers(): MorphMany
    {
        return $this->morphMany(PropertyModifier::class, 'modifiable')->orderBy('id');
    }

    /**
     * @return BelongsToMany
     */
    public function itemModifiers(): BelongsToMany
    {
        return $this->belongsToMany(ItemModifier::class, 'items_item_modifiers')
                    ->withPivot(['id', 'is_fixed'])
                    ->orderBy('is_fixed')
                    ->orderBy('pivot_id');
    }

    /**
     * @return MorphMany
     */
    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable')->orderBy('id');
    }

    /**
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('id');
    }

    /**
     * @return BelongsToMany
     */
    public function behaviours(): BelongsToMany
    {
        return $this->belongsToMany(Behaviour::class, 'items_behaviours')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsTo
     */
    public function type() : BelongsTo
    {
        return $this->belongsTo(ItemType::class, 'type_id')->withDefault(['name' => 'None']);
    }

    /**
     * @return HasOne
     */
    public function blueprintRecipe() : HasOne
    {
        return $this->hasOne(Recipe::class, 'blueprint_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function rarity() : BelongsTo
    {
        return $this->belongsTo(ItemRarity::class, 'rarity_id')->withDefault(['name' => 'None']);
    }

    /**
     * @return BelongsTo
     */
    public function suffix() : BelongsTo
    {
        return $this->belongsTo(ItemModifier::class, 'suffix_id');
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function loreI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'lore_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function consumeDescriptionI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'consume_description_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function passiveDescriptionI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'passive_description_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function bookTextI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'book_text_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function weaponSkillA() : BelongsTo
    {
        return $this->belongsTo(Skill::class, 'weapon_skill_1_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function weaponSkillB() : BelongsTo
    {
        return $this->belongsTo(Skill::class, 'weapon_skill_2_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'                        => $this->id,
            'IsEnabled'                 => $this->is_enabled,
            'NameKey'                   => $this->nameI18n->key,
            'LoreKey'                   => $this->loreI18n->key,
            'ConsumeDescriptionKey'     => $this->consumeDescriptionI18n->key,
            'PassiveDescriptionKey'     => $this->passiveDescriptionI18n->key,
            'Icon'                      => $this->icon,
            'Attachments'               => $this->attachments->present(),
            'Slot'                      => (string) $this->slot ?: self::SLOT_TYPES[0],
            'Flags'                     => \implode(', ', $this->flags ?: ['None']),
            'CurrencyType'              => (string) $this->currency_type ?: 'None',
            'SkinId'                    => (int) $this->skin_id,
            'SetId'                     => (int) $this->set_id,
            'TypeId'                    => (int) $this->type_id,
            'RarityId'                  => (int) $this->rarity_id,
            'SuffixId'                  => (int) $this->suffix_id,
            'WeaponSkillAId'            => (int) $this->weapon_skill_1_id,
            'WeaponSkillBId'            => (int) $this->weapon_skill_2_id,
            'BlueprintRecipeId'         => (int) $this->blueprintRecipe->id,
            'BlueprintRecipeItemTypeId' => (int) $this->blueprintRecipe->item->type_id,
            'ConsumeLootId'             => (int) $this->consume_loot_id,
            'ConsumeEffectId'           => (int) $this->consume_effect_id,
            'ConsumeCooldown'           => (int) $this->consume_cooldown,
            'ConsumeSound'              => (string) $this->consume_sound,
            'BookTextKey'               => (string) $this->bookTextI18n->key,
            'LearnSkillId'              => (int) $this->learn_skill_id,
            'UnlockSkillId'             => (int) $this->unlock_skill_id,
            'UnlockRelicId'             => (int) $this->unlock_relic_id,
            'UnlockScenarioId'          => (int) $this->unlock_scenario_id,
            'StackSize'                 => (int) $this->stack_size,
            'SocketCount'               => (int) $this->socket_count,
            'Level'                     => (int) $this->level,
            'RequiredLevel'             => (int) $this->required_level,
            'EnchantmentBehaviourId'    => (int) $this->enchantment_behaviour_id,
            'EnchantmentItemCategoryId' => (int) $this->enchantment_item_category_id,
            'Price'                     => $this->prices->present(),
            'AttributeModifiers'        => $this->attributeModifiers->present(),
            'PropertyModifiers'         => $this->propertyModifiers->present(),
            'Behaviours'                => $this->behaviours->pluck('id'),
            'FixedModifiers'            => $this->itemModifiers->where('pivot.is_fixed', true)->pluck('id'),
            'Modifiers'                 => $this->itemModifiers->where('pivot.is_fixed', false)->pluck('id'),
        ];
    }
}
