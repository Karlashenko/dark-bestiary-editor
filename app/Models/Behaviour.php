<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\BehaviourRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Behaviour
 *
 * @package App\Models
 */
class Behaviour extends Model implements Presentable
{
    public const TYPE_MARKER                                      = 'MarkerBehaviour';
    public const TYPE_CHANGE_OWNER                                = 'ChangeOwnerBehaviour';
    public const TYPE_AURA                                        = 'AuraBehaviour';
    public const TYPE_SPHERES                                     = 'SpheresBehaviour';
    public const TYPE_UNLOCK_SKILL                                = 'UnlockSkillBehaviour';
    public const TYPE_CREATE_LINE                                 = 'CreateLineBehaviour';
    public const TYPE_MULTISHOT                                   = 'MultishotBehaviour';
    public const TYPE_BUFF                                        = 'BuffBehaviour';
    public const TYPE_CLEAVE                                      = 'CleaveBehaviour';
    public const TYPE_SHIELD                                      = 'ShieldBehaviour';
    public const TYPE_CHANGE_MODEL                                = 'ChangeModelBehaviour';
    public const TYPE_MODIFY_STATS                                = 'ModifyStatsBehaviour';
    public const TYPE_SPIRIT_LINK                                 = 'SpiritLinkBehaviour';
    public const TYPE_ON_KILL                                     = 'OnKillBehaviour';
    public const TYPE_ON_STATUS_EFFECT                            = 'OnStatusEffectBehaviour';
    public const TYPE_ON_STATUS_EFFECT_REMOVED                    = 'OnStatusEffectRemovedBehaviour';
    public const TYPE_ON_EPISODE_START                            = 'OnEpisodeStartBehaviour';
    public const TYPE_ON_EPISODE_COMPLETE                         = 'OnEpisodeCompleteBehaviour';
    public const TYPE_ANYONE_DIED                                 = 'OnAnyoneDiedBehaviour';
    public const TYPE_ON_DEATH                                    = 'OnDeathBehaviour';
    public const TYPE_ON_USE_SKILL                                = 'OnUseSkillBehaviour';
    public const TYPE_ON_USING_SKILL                              = 'OnUsingSkillBehaviour';
    public const TYPE_ON_ENTER_CELL                               = 'OnEnterCellBehaviour';
    public const TYPE_ON_EXIT_CELL                                = 'OnExitCellBehaviour';
    public const TYPE_ON_DEAL_DAMAGE                              = 'OnDealDamageBehaviour';
    public const TYPE_ON_TAKE_DAMAGE                              = 'OnTakeDamageBehaviour';
    public const TYPE_ON_DEAL_HEAL                                = 'OnDealHealBehaviour';
    public const TYPE_ON_TAKE_HEAL                                = 'OnTakeHealBehaviour';
    public const TYPE_ON_CONTACT                                  = 'OnContactBehaviour';
    public const TYPE_ON_BLOCK                                    = 'OnBlockBehaviour';
    public const TYPE_ON_COMBAT_START                             = 'OnCombatStartBehaviour';
    public const TYPE_ON_SCENARIO_START                           = 'OnScenarioStartBehaviour';
    public const TYPE_ON_END_TURN                                 = 'OnEndTurnBehaviour';
    public const TYPE_ON_HEALTH_DROPS_BELOW                       = 'OnHealthDropsBelowBehaviour';
    public const TYPE_PER_UNIT_IN_RANGE                           = 'PerUnitInRangeBehaviour';
    public const TYPE_PER_SURROUNDING_ENEMY_DAMAGE_BEHAVIOUR      = 'PerSurroundingEnemyDamageBehaviour';
    public const TYPE_PER_MISSING_HEALTH_PERCENT_DAMAGE_BEHAVIOUR = 'PerMissingHealthPercentDamageBehaviour';
    public const TYPE_PER_RANGE_DAMAGE_BEHAVIOUR                  = 'PerRangeDamageBehaviour';
    public const TYPE_SUMMONED_DAMAGE_BEHAVIOUR                   = 'SummonedDamageBehaviour';
    public const TYPE_HEALTH_FRACTION_DAMAGE_BEHAVIOUR            = 'HealthFractionDamageBehaviour';
    public const TYPE_RANGE_DAMAGE_BEHAVIOUR                      = 'RangeDamageBehaviour';
    public const TYPE_STATUS_EFFECT_DAMAGE_BEHAVIOUR              = 'StatusEffectDamageBehaviour';
    public const TYPE_BACKSTAB_DAMAGE_BEHAVIOUR                   = 'BackstabDamageBehaviour';
    public const TYPE_SET_BEHAVIOUR                               = 'SetBehaviour';
    public const TYPE_ITEM_BASED_BEHAVIOUR                        = 'ItemBasedBehaviour';
    public const TYPE_DUAL_WIELD_BEHAVIOUR                        = 'DualWieldBehaviour';

    public const TYPES = [
        self::TYPE_MARKER,
        self::TYPE_CHANGE_OWNER,
        self::TYPE_AURA,
        self::TYPE_SPHERES,
        self::TYPE_UNLOCK_SKILL,
        self::TYPE_CREATE_LINE,
        self::TYPE_MULTISHOT,
        self::TYPE_CHANGE_MODEL,
        self::TYPE_MODIFY_STATS,
        self::TYPE_SPIRIT_LINK,
        self::TYPE_CLEAVE,
        self::TYPE_BUFF,
        self::TYPE_SHIELD,
        self::TYPE_ON_KILL,
        self::TYPE_ON_STATUS_EFFECT,
        self::TYPE_ON_STATUS_EFFECT_REMOVED,
        self::TYPE_ANYONE_DIED,
        self::TYPE_ON_EPISODE_START,
        self::TYPE_ON_EPISODE_COMPLETE,
        self::TYPE_ON_DEATH,
        self::TYPE_ON_USE_SKILL,
        self::TYPE_ON_USING_SKILL,
        self::TYPE_ON_ENTER_CELL,
        self::TYPE_ON_EXIT_CELL,
        self::TYPE_ON_DEAL_DAMAGE,
        self::TYPE_ON_TAKE_DAMAGE,
        self::TYPE_ON_DEAL_HEAL,
        self::TYPE_ON_TAKE_HEAL,
        self::TYPE_ON_CONTACT,
        self::TYPE_ON_BLOCK,
        self::TYPE_ON_COMBAT_START,
        self::TYPE_ON_SCENARIO_START,
        self::TYPE_ON_END_TURN,
        self::TYPE_ON_HEALTH_DROPS_BELOW,
        self::TYPE_PER_UNIT_IN_RANGE,
        self::TYPE_PER_SURROUNDING_ENEMY_DAMAGE_BEHAVIOUR,
        self::TYPE_PER_MISSING_HEALTH_PERCENT_DAMAGE_BEHAVIOUR,
        self::TYPE_PER_RANGE_DAMAGE_BEHAVIOUR,
        self::TYPE_SUMMONED_DAMAGE_BEHAVIOUR,
        self::TYPE_HEALTH_FRACTION_DAMAGE_BEHAVIOUR,
        self::TYPE_RANGE_DAMAGE_BEHAVIOUR,
        self::TYPE_STATUS_EFFECT_DAMAGE_BEHAVIOUR,
        self::TYPE_BACKSTAB_DAMAGE_BEHAVIOUR,
        self::TYPE_SET_BEHAVIOUR,
        self::TYPE_ITEM_BASED_BEHAVIOUR,
        self::TYPE_DUAL_WIELD_BEHAVIOUR,
    ];

    public const FLAGS = [
        'Temporary',
        'Defensive',
        'Offensive',
        'Positive',
        'Negative',
        'Physical',
        'Magical',
        'Hidden',
        'Dispellable',
        'BreaksOnDealDamage',
        'BreaksOnDealDirectDamage',
        'BreaksOnTakeDamage',
        'BreaksOnCasterDeath',
        'BreaksOnCrowdControl',
        'BreaksOnCast',
        'BreaksOnEndTurn',
        'IgnoreImmunity',
        'DoNotRemoveOnDeath',
        'MonsterModifier',
        'MonsterAffix',
        'EpisodeAffix',
        'ItemAffix',
        'Food',
        'Ascension',
        'Unique',
    ];

    public const RE_APPLY_FLAGS = [
        'RefreshDuration',
        'RefreshEffect',
        'StackDuration',
        'StackEffect',
    ];

    public const STATUS_FLAGS = [
        'Slow',
        'Stun',
        'Disarm',
        'Silence',
        'Immortal',
        'Weakness',
        'Swiftness',
        'Adrenaline',
        'Invisibility',
        'Immobilization',
        'Invulnerability',
        'Blind',
		'Sleep',
		'Bleeding',
		'Burning',
		'Poison',
		'Confusion',
		'Taunt',
		'Polymorph',
		'Freecasting',
		'MindControl',
		'Undead',
        'CausticPoison',
    ];

    /**
     * @param Behaviour        $behaviour
     * @param BehaviourRequest $request
     *
     * @return Behaviour
     * @throws \Exception
     * @throws \Throwable
     */
    public static function populate(Behaviour $behaviour, BehaviourRequest $request): self
    {
        DB::transaction(function () use ($behaviour, $request) {
            $behaviour->type = (string) $request->type;
            $behaviour->label = (string) $request->label;
            $behaviour->tint = (string) $request->tint;
            $behaviour->scale = (float) $request->scale;
            $behaviour->suffix = (string) $request->suffix;
            $behaviour->name_i18n_id = $request->name_i18n_id;
            $behaviour->description_i18n_id = $request->description_i18n_id;
            $behaviour->rarity_id = $request->rarity_id;
            $behaviour->icon = (string) $request->icon;
            $behaviour->flags = (array) $request->flags;
            $behaviour->re_apply_flags = (array) $request->re_apply_flags;
            $behaviour->status_flags = (array) $request->status_flags;
            $behaviour->status_immunity = (array) $request->status_immunity;
            $behaviour->period = (int) $request->period;
            $behaviour->duration = (int) $request->duration;
            $behaviour->caster_is_bearer = (bool) $request->caster_is_bearer;
            $behaviour->stack_count_max = min(1, (int) $request->stack_count_max);
            $behaviour->change_model = (string) $request->change_model;
            $behaviour->initial_effect_id = $request->initial_effect_id;
            $behaviour->periodic_effect_id = $request->periodic_effect_id;
            $behaviour->on_expire_effect_id = $request->on_expire_effect_id;
            $behaviour->on_remove_effect_id = $request->on_remove_effect_id;

            $behaviour->on_status_effect_removed_effect_id = $request->on_status_effect_removed_effect_id;
            $behaviour->on_status_effect_removed_status_flags = (array) $request->on_status_effect_removed_status_flags;

            $behaviour->on_status_effect_behaviour_id = $request->on_status_effect_behaviour_id;
            $behaviour->on_status_effect_status_flags = (array) $request->on_status_effect_status_flags;

            $behaviour->create_line_prefab = (string) $request->create_line_prefab;

            $behaviour->damage_flags = (array) $request->damage_flags;
            $behaviour->damage_flags_exclude = (array) $request->damage_flags_exclude;
            $behaviour->damage_status_flags = (array) $request->damage_status_flags;
            $behaviour->damage_info_flags = (array) $request->damage_info_flags;
            $behaviour->damage_info_flags_exclude = (array) $request->damage_info_flags_exclude;

            $behaviour->on_health_drops_below_fraction = (float) $request->on_health_drops_below_fraction;
            $behaviour->on_health_drops_below_effect_id = $request->on_health_drops_below_effect_id;
            $behaviour->on_death_effect_id = $request->on_death_effect_id;
            $behaviour->on_kill_effect_id = $request->on_kill_effect_id;
            $behaviour->on_enter_cell_effect_id = $request->on_enter_cell_effect_id;
            $behaviour->on_exit_cell_effect_id = $request->on_exit_cell_effect_id;
            $behaviour->on_take_damage_effect_id = $request->on_take_damage_effect_id;
            $behaviour->on_deal_damage_effect_id = $request->on_deal_damage_effect_id;
            $behaviour->on_take_heal_effect_id = $request->on_take_heal_effect_id;
            $behaviour->on_deal_heal_effect_id = $request->on_deal_heal_effect_id;
            $behaviour->on_episode_start_effect_id = $request->on_episode_start_effect_id;
            $behaviour->on_episode_complete_effect_id = $request->on_episode_complete_effect_id;
            $behaviour->on_end_turn_effect_id = $request->on_end_turn_effect_id;

            $behaviour->on_contact_radius = (int) $request->on_contact_radius;
            $behaviour->on_contact_effect_id = $request->on_contact_effect_id;

            $behaviour->on_use_skill_flags = (array) $request->on_use_skill_flags;
            $behaviour->on_use_skill_effect_id = $request->on_use_skill_effect_id;
            $behaviour->on_use_skill_rarity_id = $request->on_use_skill_rarity_id;

            $behaviour->on_block_effect_id = $request->on_block_effect_id;

            $behaviour->on_combat_start_effect_id = $request->on_combat_start_effect_id;

            $behaviour->unlock_skill_id = $request->unlock_skill_id;

            $behaviour->aura_range = (int) $request->aura_range;
            $behaviour->aura_behaviour_id = $request->aura_behaviour_id;

            $behaviour->dual_wield_behaviour_id = $request->dual_wield_behaviour_id;

            $behaviour->spirit_link_fraction = (float) $request->spirit_link_fraction;

            $behaviour->spheres_prefab = (string) $request->spheres_prefab;

            $behaviour->shield_max_amount_formula = (string) $request->shield_max_amount_formula;

            $behaviour->on_event_subject = (string) $request->on_event_subject;

            $behaviour->cleave_fraction = (float) $request->cleave_fraction;

            $behaviour->item_based_behaviour_id = $request->item_based_behaviour_id;
            $behaviour->item_based_category_id = $request->item_based_category_id;

            $behaviour->damage_minimum_number_of_enemies = (int) $request->damage_minimum_number_of_enemies;
            $behaviour->damage_modifier_type = (string) $request->damage_modifier_type;
            $behaviour->damage_types = (array) $request->damage_types;
            $behaviour->damage_target = (string) $request->damage_target;
            $behaviour->damage_comparator = (string) $request->damage_comparator;
            $behaviour->damage_value = (float) $request->damage_value;
            $behaviour->damage_amount = (float) $request->damage_amount;
            $behaviour->damage_min = (float) $request->damage_min;
            $behaviour->damage_max = (float) $request->damage_max;

            $behaviour->save();

            $behaviour->attributeModifiers()->delete();

            foreach ((array) $request->get('attribute_modifiers', []) as $data) {
                $modifier = new AttributeModifier();
                $modifier->modifiable_id = $behaviour->id;
                $modifier->modifiable_type = \get_class($behaviour);
                $modifier->attribute_id = \array_get($data, 'attribute_id');
                $modifier->amount = (float) \array_get($data, 'amount');
                $modifier->type = (string) \array_get($data, 'type');
                $modifier->fraction = (float) \array_get($data, 'fraction');
                $modifier->fraction_attribute_id = \array_get($data, 'fraction_attribute_id');
                $modifier->max_attribute_fraction = (float) \array_get($data, 'max_attribute_fraction');
                $modifier->save();
            }

            $behaviour->propertyModifiers()->delete();

            foreach ((array) $request->get('property_modifiers', []) as $data) {
                $modifier = new PropertyModifier();
                $modifier->modifiable_id = $behaviour->id;
                $modifier->modifiable_type = \get_class($behaviour);
                $modifier->property_id = \array_get($data, 'property_id');
                $modifier->amount = (float) \array_get($data, 'amount');
                $modifier->type = (string) \array_get($data, 'type');
                $modifier->fraction = (float) \array_get($data, 'fraction');
                $modifier->fraction_property_id = \array_get($data, 'fraction_property_id');
                $modifier->fraction_attribute_id = \array_get($data, 'fraction_attribute_id');
                $modifier->max_attribute_fraction = (float) \array_get($data, 'max_attribute_fraction');
                $modifier->formula = (string) \array_get($data, 'formula');
                $modifier->save();
            }

            $behaviour->attachments()->delete();

            foreach ((array) $request->get('attachments', []) as $data) {
                $attachment = new Attachment();
                $attachment->point = (string) \array_get($data, 'point');
                $attachment->prefab = (string) \array_get($data, 'prefab');
                $attachment->rotate = (bool) \array_get($data, 'rotate');
                $attachment->rotate_same_as_caster = (bool) \array_get($data, 'rotate_same_as_caster');
                $attachment->flip_rotation = (bool) \array_get($data, 'flip_rotation');
                $attachment->original_scale = (bool) \array_get($data, 'original_scale');
                $attachment->attachable_id = $behaviour->id;
                $attachment->attachable_type = __CLASS__;
                $attachment->save();
            }

            $behaviour->behaviours()->sync([]);

            foreach ((array) $request->get('behaviours', []) as $data) {
                $behaviour->behaviours()->attach((int) \array_get($data, 'id'));
            }

            $behaviour->validators()->sync([]);

            foreach ((array) $request->get('validators', []) as $data) {
                $validatorId = (int) \array_get($data, 'pivot.validator_id');
                $validatorPurpose = (string) \array_get($data, 'pivot.validator_purpose');
                $behaviour->validators()->attach($validatorId, ['validator_purpose' => $validatorPurpose]);
            }

            $behaviour->itemCategories()->sync([]);

            foreach ((array) $request->get('item_categories', []) as $data) {
                $behaviour->itemCategories()->attach((int) \array_get($data, 'pivot.item_category_id'));
            }
        });

        $behaviour->load($behaviour->with);

        return $behaviour;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'behaviours';
        $this->timestamps = false;
        $this->with = [
            'nameI18n',
            'descriptionI18n',
            'attributeModifiers',
            'propertyModifiers',
            'itemCategories',
            'attachments',
            'behaviours',
            'validators',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['flags'] = 'array';
        $casts['re_apply_flags'] = 'array';
        $casts['status_flags'] = 'array';
        $casts['status_immunity'] = 'array';
        $casts['damage_types'] = 'array';
        $casts['damage_flags'] = 'array';
        $casts['damage_flags_exclude'] = 'array';
        $casts['damage_status_flags'] = 'array';
        $casts['damage_info_flags'] = 'array';
        $casts['damage_info_flags_exclude'] = 'array';
        $casts['on_use_skill_flags'] = 'array';
        $casts['on_status_effect_removed_status_flags'] = 'array';
        $casts['on_status_effect_status_flags'] = 'array';

        return $casts;
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
    public function descriptionI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')
                    ->withDefault();
    }

    /**
     * @return MorphMany
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('id');
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
    public function behaviours(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'behaviours_behaviours', 'parent_id', 'child_id');
    }

    /**
     * @return BelongsToMany
     */
    public function validators(): BelongsToMany
    {
        return $this->belongsToMany(Validator::class, 'behaviours_validators')
                    ->withPivot(['id', 'validator_purpose'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function itemCategories(): BelongsToMany
    {
        return $this->belongsToMany(ItemCategory::class, 'behaviours_item_categories')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        $presentation = [
            'Id'                => $this->id,
            'Type'              => $this->type,
            'Label'             => $this->label,
            'NameKey'           => $this->nameI18n->key,
            'DescriptionKey'    => $this->descriptionI18n->key,
            'RarityId'          => (int) $this->rarity_id,
            'Icon'              => $this->icon,
            'Tint'              => (string) $this->tint,
            'Scale'             => (float) $this->scale,
            'CasterIsBearer'    => (bool) $this->caster_is_bearer,
            'Flags'             => \implode(', ', $this->flags ?: ['None']),
            'ReApplyFlags'      => \implode(', ', $this->re_apply_flags ?: ['None']),
            'StatusFlags'       => \implode(', ', $this->status_flags ?: ['None']),
            'StatusImmunity'    => \implode(', ', $this->status_immunity ?: ['None']),
            'Period'            => (int) $this->period,
            'Duration'          => (int) $this->duration,
            'StackCountMax'     => (int) $this->stack_count_max,
            'OnExpireEffectId'  => (int) $this->on_expire_effect_id,
            'OnRemoveEffectId'  => (int) $this->on_remove_effect_id,
            'Attachments'       => $this->attachments->present(),
            'EventSubject'      => (string) $this->on_event_subject ?: 'Me',
            'ItemCategories'    => $this->itemCategories->map(function (ItemCategory $itemCategory) {return $itemCategory->id;})->toArray(),
            'Validators'        => $this->validators->map(function (Validator $validator) {
                return [
                    'ValidatorId'      => $validator->id,
                    'ValidatorPurpose' => $validator->pivot->validator_purpose,
                ];
            })->toArray(),
        ];

        if ($this->type === self::TYPE_BUFF) {
            $presentation['InitialEffectId'] = (int) $this->initial_effect_id;
            $presentation['PeriodicEffectId'] = (int) $this->periodic_effect_id;
        }

        if ($this->type === self::TYPE_ITEM_BASED_BEHAVIOUR) {
            $presentation['BehaviourId'] = (int) $this->item_based_behaviour_id;
            $presentation['ItemCategoryId'] = (int) $this->item_based_category_id;
        }

        if ($this->type === self::TYPE_ON_STATUS_EFFECT_REMOVED) {
            $presentation['EffectId'] = (int) $this->on_status_effect_removed_effect_id;
            $presentation['StatusFlags'] = \implode(', ', $this->on_status_effect_removed_status_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_ON_STATUS_EFFECT) {
            $presentation['BehaviourId'] = (int) $this->on_status_effect_behaviour_id;
            $presentation['StatusFlags'] = \implode(', ', $this->on_status_effect_status_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_CREATE_LINE) {
            $presentation['Prefab'] = (string) $this->create_line_prefab;
        }

        if ($this->type === self::TYPE_AURA || $this->type === self::TYPE_PER_UNIT_IN_RANGE) {
            $presentation['Range'] = (int) $this->aura_range;
            $presentation['BehaviourId'] = (int) $this->aura_behaviour_id;
        }

        if ($this->type === self::TYPE_UNLOCK_SKILL) {
            $presentation['SkillId'] = (int) $this->unlock_skill_id;
        }

        if ($this->type === self::TYPE_ON_COMBAT_START || $this->type === self::TYPE_ON_SCENARIO_START) {
            $presentation['EffectId'] = (int) $this->on_combat_start_effect_id;
        }

        if ($this->type === self::TYPE_DUAL_WIELD_BEHAVIOUR) {
            $presentation['BehaviourId'] = (int) $this->dual_wield_behaviour_id;
        }

        if ($this->type === self::TYPE_MODIFY_STATS) {
            $presentation['AttributeModifiers'] = $this->attributeModifiers->present();
            $presentation['PropertyModifiers'] = $this->propertyModifiers->present();
        }

        if ($this->type === self::TYPE_SPIRIT_LINK) {
            $presentation['Fraction'] = (float) $this->spirit_link_fraction;
        }

        if ($this->type === self::TYPE_CHANGE_MODEL) {
            $presentation['Model'] = $this->change_model;
        }

        if ($this->type === self::TYPE_SPHERES) {
            $presentation['Prefab'] = $this->spheres_prefab;
        }

        if ($this->type === self::TYPE_SHIELD) {
            $presentation['MaxAmountFormula'] = (string) $this->shield_max_amount_formula;
        }

        if ($this->type === self::TYPE_CLEAVE) {
            $presentation['Fraction'] = (float) $this->cleave_fraction;
        }

        if ($this->type === self::TYPE_SET_BEHAVIOUR) {
            $presentation['Behaviours'] = $this->behaviours->pluck('id')->toArray();
        }

        if ($this->type === self::TYPE_ON_DEAL_HEAL) {
            $presentation['EffectId'] = (int) $this->on_deal_heal_effect_id;
        }

        if ($this->type === self::TYPE_ON_TAKE_HEAL) {
            $presentation['EffectId'] = (int) $this->on_take_heal_effect_id;
        }

        if ($this->type === self::TYPE_ON_EPISODE_START) {
            $presentation['EffectId'] = (int) $this->on_episode_start_effect_id;
        }

        if ($this->type === self::TYPE_ON_EPISODE_COMPLETE) {
            $presentation['EffectId'] = (int) $this->on_episode_complete_effect_id;
        }

        if ($this->type === self::TYPE_ON_END_TURN) {
            $presentation['EffectId'] = (int) $this->on_end_turn_effect_id;
        }

        if ($this->type === self::TYPE_ON_CONTACT) {
            $presentation['EffectId'] = (int) $this->on_contact_effect_id;
            $presentation['Radius'] = (int) $this->on_contact_radius;
        }

        if ($this->type === self::TYPE_ON_BLOCK) {
            $presentation['EffectId'] = (int) $this->on_block_effect_id;
        }

        if ($this->type === self::TYPE_ON_USE_SKILL || $this->type === self::TYPE_ON_USING_SKILL) {
            $presentation['EffectId'] = (int) $this->on_use_skill_effect_id;
            $presentation['SkillFlags'] = \implode(', ', $this->on_use_skill_flags ?: ['None']);
            $presentation['SkillRarityId'] = (int) $this->on_use_skill_rarity_id;
        }

        if ($this->type === self::TYPE_ON_HEALTH_DROPS_BELOW) {
            $presentation['EffectId'] = (int) $this->on_health_drops_below_effect_id;
            $presentation['Fraction'] = (float) $this->on_health_drops_below_fraction;
        }

        if ($this->type === self::TYPE_ON_TAKE_DAMAGE || $this->type === self::TYPE_ON_DEAL_DAMAGE) {
            $presentation['DamageFlags'] = \implode(', ', $this->damage_flags ?: ['None']);
            $presentation['DamageInfoFlags'] = \implode(', ', $this->damage_info_flags ?: ['None']);
            $presentation['ExcludeDamageFlags'] = \implode(', ', $this->damage_flags_exclude ?: ['None']);
            $presentation['ExcludeDamageInfoFlags'] = \implode(', ', $this->damage_info_flags_exclude ?: ['None']);
            $presentation['DamageTypes'] = $this->damage_types;

            if ($this->type === self::TYPE_ON_TAKE_DAMAGE) {
                $presentation['EffectId'] = (int) $this->on_take_damage_effect_id;
            }

            if ($this->type === self::TYPE_ON_DEAL_DAMAGE) {
                $presentation['EffectId'] = (int) $this->on_deal_damage_effect_id;
            }
        }

        if ($this->type === self::TYPE_ON_DEATH) {
            $presentation['EffectId'] = (int) $this->on_death_effect_id;
        }

        if ($this->type === self::TYPE_ON_KILL || $this->type === self::TYPE_ANYONE_DIED) {
            $presentation['EffectId'] = (int) $this->on_kill_effect_id;
            $presentation['DamageFlags'] = \implode(', ', $this->damage_flags ?: ['None']);
            $presentation['DamageInfoFlags'] = \implode(', ', $this->damage_info_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_ON_ENTER_CELL) {
            $presentation['EffectId'] = (int) $this->on_enter_cell_effect_id;
        }

        if ($this->type === self::TYPE_ON_EXIT_CELL) {
            $presentation['EffectId'] = (int) $this->on_exit_cell_effect_id;
        }

        if (\strpos($this->type, 'DamageBehaviour') !== false) {
            $presentation['ModifierType'] = (string) $this->damage_modifier_type;
            $presentation['DamageTypes'] = $this->damage_types;
        }

        if ($this->type === self::TYPE_PER_SURROUNDING_ENEMY_DAMAGE_BEHAVIOUR) {
            $presentation['MinimumNumberOfEnemies'] = (int) $this->damage_minimum_number_of_enemies;
            $presentation['AmountPerEnemy'] = (float) $this->damage_amount;
            $presentation['Range'] = (float) $this->damage_value;
            $presentation['Min'] = (float) $this->damage_min;
            $presentation['Max'] = (float) $this->damage_max;
        }

        if ($this->type === self::TYPE_HEALTH_FRACTION_DAMAGE_BEHAVIOUR) {
            $presentation['Target'] = (string) $this->damage_target;
            $presentation['RequiredHealthFraction'] = (string) $this->damage_value;
            $presentation['Comparator'] = (string) $this->damage_comparator;
            $presentation['Amount'] = (float) $this->damage_amount;
        }

        if ($this->type === self::TYPE_SUMMONED_DAMAGE_BEHAVIOUR) {
            $presentation['Amount'] = (float) $this->damage_amount;
        }

        if ($this->type === self::TYPE_PER_MISSING_HEALTH_PERCENT_DAMAGE_BEHAVIOUR) {
            $presentation['Target'] = (string) $this->damage_target;
            $presentation['AmountPerPercent'] = (float) $this->damage_amount;
            $presentation['Min'] = (float) $this->damage_min;
            $presentation['Max'] = (float) $this->damage_max;
        }

        if ($this->type === self::TYPE_RANGE_DAMAGE_BEHAVIOUR) {
            $presentation['Range'] = (float) $this->damage_value;
            $presentation['Comparator'] = (float) $this->damage_comparator;
            $presentation['Amount'] = (float) $this->damage_amount;
        }

        if ($this->type === self::TYPE_PER_RANGE_DAMAGE_BEHAVIOUR) {
            $presentation['DamageFlags'] = \implode(', ', $this->damage_flags ?: ['None']);
            $presentation['DamageInfoFlags'] = \implode(', ', $this->damage_info_flags ?: ['None']);
            $presentation['AmountPerCell'] = (float) $this->damage_amount;
            $presentation['Min'] = (float) $this->damage_min;
            $presentation['Max'] = (float) $this->damage_max;
        }

        if ($this->type === self::TYPE_STATUS_EFFECT_DAMAGE_BEHAVIOUR) {
            $presentation['DamageStatusFlags'] = \implode(', ', $this->damage_status_flags ?: ['None']);
            $presentation['Amount'] = (float) $this->damage_amount;
        }

        if ($this->type === self::TYPE_BACKSTAB_DAMAGE_BEHAVIOUR) {
            $presentation['Amount'] = (float) $this->damage_amount;
        }

        return $presentation;
    }
}
