<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\EffectRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Effect
 *
 * @package App\Models
 */
class Effect extends Model implements Presentable
{
    public const TYPE_SET                        = 'EffectSet';
    public const TYPE_REMOVE_SHIELDS             = 'RemoveShieldsEffect';
    public const TYPE_HOOK                       = 'HookEffect';
    public const TYPE_MIRROR_IMAGE               = 'MirrorImageEffect';
    public const TYPE_REWARD                     = 'RewardEffect';
    public const TYPE_SUCK_IN                    = 'SuckInEffect';
    public const TYPE_DISPEL                     = 'DispelEffect';
    public const TYPE_REMOVE_BEHAVIOUR           = 'RemoveBehaviourEffect';
    public const TYPE_STEAL_BEHAVIOUR            = 'StealBehaviourEffect';
    public const TYPE_HEAL                       = 'HealEffect';
    public const TYPE_HIDE                       = 'HideEffect';
    public const TYPE_MOVE                       = 'MoveEffect';
    public const TYPE_ATTACK                     = 'AttackEffect';
    public const TYPE_SHOW                       = 'ShowEffect';
    public const TYPE_SHIELD                     = 'ShieldEffect';
    public const TYPE_DRAG                       = 'DragEffect';
    public const TYPE_CHAIN                      = 'ChainEffect';
    public const TYPE_TELEPORTATION              = 'TeleportationEffect';
    public const TYPE_TELEPORT_BEHIND_TARGET     = 'TeleportBehindTargetEffect';
    public const TYPE_ADD_CURRENCY               = 'AddCurrencyEffect';
    public const TYPE_RUN_AWAY                   = 'RunAwayEffect';
    public const TYPE_REPEAT                     = 'RepeatEffect';
    public const TYPE_KNOCKBACK                  = 'KnockbackEffect';
    public const TYPE_RESTORE_RESOURCE           = 'RestoreResourceEffect';
    public const TYPE_DAMAGE                     = 'DamageEffect';
    public const TYPE_DAMAGE_WEAPON              = 'WeaponDamageEffect';
    public const TYPE_DAMAGE_RANDOM_ELEMENT      = 'RandomElementDamageEffect';
    public const TYPE_DAMAGE_PER_BEHAVIOUR_STACK = 'PerBehaviourStackDamageEffect';
    public const TYPE_LAUNCH_MISSILE             = 'LaunchMissileEffect';
    public const TYPE_LAUNCH_AOE_MISSILE         = 'LaunchAOEMissileEffect';
    public const TYPE_LAUNCH_WEAPON_MISSILE      = 'LaunchWeaponMissileEffect';
    public const TYPE_LAUNCH_MISSILE_FROM_TARGET = 'LaunchMissileFromTargetEffect';
    public const TYPE_HEAL_FROM_TARGET_HEALTH    = 'HealFromTargetHealthEffect';
    public const TYPE_DESTROY_EQUIPPED_ITEM      = 'DestroyEquippedItemEffect';
    public const TYPE_SEARCH_BEHIND              = 'SearchBehindEffect';
    public const TYPE_SEARCH_DUMMIES             = 'SearchDummiesEffect';
    public const TYPE_SEARCH_PERIMETER           = 'SearchPerimeterEffect';
    public const TYPE_SEARCH_AREA                = 'SearchAreaEffect';
    public const TYPE_SEARCH_POINTS              = 'SearchPointsEffect';
    public const TYPE_SEARCH_LINE                = 'SearchLineEffect';
    public const TYPE_SEARCH_RANDOM_POINTS       = 'SearchRandomPoints';
    public const TYPE_SEARCH_CORPSES             = 'SearchCorpsesEffect';
    public const TYPE_APPLY_BEHAVIOUR            = 'ApplyBehaviourEffect';
    public const TYPE_CREATE_UNIT                = 'CreateUnitEffect';
    public const TYPE_CREATE_BEAM                = 'CreateBeamEffect';
    public const TYPE_CREATE_SOUND               = 'CreateSoundEffect';
    public const TYPE_WAIT                       = 'WaitEffect';
    public const TYPE_RANDOM_WAIT                = 'RandomWaitEffect';
    public const TYPE_KILL                       = 'KillEffect';
    public const TYPE_REDUCE_COOLDOWNS           = 'ReduceCooldownsEffect';
    public const TYPE_RUN_COOLDOWN               = 'RunCooldownEffect';
    public const TYPE_RANDOM                     = 'RandomEffect';
    public const TYPE_IF_ELSE                    = 'IfElseEffect';

    public const TYPES = [
        self::TYPE_SET,
        self::TYPE_REMOVE_SHIELDS,
        self::TYPE_HOOK,
        self::TYPE_MIRROR_IMAGE,
        self::TYPE_REWARD,
        self::TYPE_SUCK_IN,
        self::TYPE_HEAL,
        self::TYPE_HIDE,
        self::TYPE_MOVE,
        self::TYPE_ATTACK,
        self::TYPE_DISPEL,
        self::TYPE_REMOVE_BEHAVIOUR,
        self::TYPE_STEAL_BEHAVIOUR,
        self::TYPE_SHOW,
        self::TYPE_SHIELD,
        self::TYPE_DRAG,
        self::TYPE_CHAIN,
        self::TYPE_ADD_CURRENCY,
        self::TYPE_TELEPORTATION,
        self::TYPE_TELEPORT_BEHIND_TARGET,
        self::TYPE_REPEAT,
        self::TYPE_RUN_AWAY,
        self::TYPE_KNOCKBACK,
        self::TYPE_RESTORE_RESOURCE,
        self::TYPE_DAMAGE,
        self::TYPE_DAMAGE_WEAPON,
        self::TYPE_DAMAGE_RANDOM_ELEMENT,
        self::TYPE_DAMAGE_PER_BEHAVIOUR_STACK,
        self::TYPE_LAUNCH_MISSILE,
        self::TYPE_LAUNCH_AOE_MISSILE,
        self::TYPE_LAUNCH_WEAPON_MISSILE,
        self::TYPE_LAUNCH_MISSILE_FROM_TARGET,
        self::TYPE_HEAL_FROM_TARGET_HEALTH,
        self::TYPE_DESTROY_EQUIPPED_ITEM,
        self::TYPE_SEARCH_BEHIND,
        self::TYPE_SEARCH_DUMMIES,
        self::TYPE_SEARCH_PERIMETER,
        self::TYPE_SEARCH_AREA,
        self::TYPE_SEARCH_POINTS,
        self::TYPE_SEARCH_LINE,
        self::TYPE_SEARCH_RANDOM_POINTS,
        self::TYPE_SEARCH_CORPSES,
        self::TYPE_APPLY_BEHAVIOUR,
        self::TYPE_CREATE_UNIT,
        self::TYPE_CREATE_BEAM,
        self::TYPE_CREATE_SOUND,
        self::TYPE_WAIT,
        self::TYPE_RANDOM_WAIT,
        self::TYPE_KILL,
        self::TYPE_REDUCE_COOLDOWNS,
        self::TYPE_RUN_COOLDOWN,
        self::TYPE_RANDOM,
        self::TYPE_IF_ELSE,
    ];

    public const OWNER_TYPES = [
        'Auto',
        'Neutral',
    ];

    public const HEAL_FLAGS = [
        'Vampirism',
        'Regeneration',
    ];

    public const DAMAGE_FLAGS = [
        'CantBeCritical',
        'CantBeDodged',
        'CantBeBlocked',
        'Melee',
        'Magic',
        'Ranged',
        'True',
        'DOT',
    ];

    public const DAMAGE_INFO_FLAGS = [
        'Critical',
        'Backstab',
        'Dodged',
        'Blocked',
        'Reflected',
    ];

    public const TARGET_TYPES = [
        'Caster',
        'CasterPoint',
        'Target',
        'TargetPoint',
    ];

    public const DAMAGE_TYPES = [
        'Crushing',
        'Slashing',
        'Piercing',
        'Fire',
        'Cold',
        'Lightning',
        'Holy',
        'Shadow',
        'Arcane',
        'Poison',
        'Chaos',
        'Health',
    ];

    public const WEAPON_SOUND_TYPES = [
        'None',
        'Weapon',
        'Axe',
        'Mace',
        'Fist',
        'Sword',
        'Shield',
        'Arrow',
        'Dagger',
    ];

    public const DAMAGE_TARGETS = [
        'Victim',
        'Attacker',
    ];

    /**
     * @param Effect        $effect
     * @param EffectRequest $request
     *
     * @return Effect
     * @throws \Throwable
     * @throws \Exception
     */
    public static function populate(Effect $effect, EffectRequest $request): self
    {
        DB::transaction(function () use ($effect, $request) {
            $effect->name = (string) $request->name;
            $effect->type = (string) $request->type;
            $effect->sound = (string) $request->sound;
            $effect->animation = (string) $request->animation;
            $effect->target = (string) $request->target ?: 'Target';
            $effect->formula_id = $request->formula_id;
            $effect->formula_text = (string) $request->formula_text;
            $effect->chance = $request->chance > 0 ? (float) $request->chance : 1;
            $effect->stack_chance = (bool) $request->stack_chance;
            $effect->caster_is_owner = (bool) $request->caster_is_owner;

            $effect->restore_resource_type = (string) $request->restore_resource_type;
            $effect->restore_resource_amount = (float) $request->restore_resource_amount;

            $effect->currency_type = (string) $request->currency_type;
            $effect->currency_formula = (string) $request->currency_formula;

            $effect->if_else_if_effect_id = $request->if_else_if_effect_id;
            $effect->if_else_else_effect_id = $request->if_else_else_effect_id;

            $effect->reward_id = $request->reward_id;

            $effect->destroy_equipped_item_id = $request->destroy_equipped_item_id;

            $effect->run_cooldown_skill_id = $request->run_cooldown_skill_id;

            $effect->hook_hook = (string) $request->hook_hook;
            $effect->hook_beam = (string) $request->hook_beam;

            $effect->repeat_times = (int) $request->repeat_times;
            $effect->repeat_effect_id = $request->repeat_effect_id;

            $effect->suck_in_radius = (int) $request->suck_in_radius;
            $effect->suck_in_duration = (float) $request->suck_in_duration;
            $effect->suck_in_animation = (string) $request->suck_in_animation;

            $effect->heal_from_target_health_fraction = (float) $request->heal_from_target_health_fraction;

            $effect->reduce_cooldowns_amount = (int) $request->reduce_cooldowns_amount;

            $effect->mirror_image_behaviour_id = $request->mirror_image_behaviour_id;
            $effect->mirror_image_prefab = (string) $request->mirror_image_prefab;
            $effect->mirror_image_duration = (int) $request->mirror_image_duration;
            $effect->mirror_image_count = (int) $request->mirror_image_count;

            $effect->random_wait_min = (float) $request->random_wait_min;
            $effect->random_wait_max = (float) $request->random_wait_max;

            $effect->dispell_behaviour_flags = (array) $request->dispell_behaviour_flags;
            $effect->dispell_behaviour_status_flags = (array) $request->dispell_behaviour_status_flags;

            $effect->remove_behaviour_behaviour_id = $request->remove_behaviour_behaviour_id;

            $effect->drag_start_animation = (string) $request->drag_start_animation;
            $effect->drag_end_animation = (string) $request->drag_end_animation;
            $effect->drag_mover_type = (string) $request->drag_mover_type;
            $effect->drag_mover_speed = (float) $request->drag_mover_speed;
            $effect->drag_mover_height = (float) $request->drag_mover_height;
            $effect->drag_collide_with_environment_effect_id = $request->drag_collide_with_environment_effect_id;
            $effect->drag_collide_with_entities_effect_id = $request->drag_collide_with_entities_effect_id;
            $effect->drag_enter_cell_effect_id = $request->drag_enter_cell_effect_id;
            $effect->drag_exit_cell_effect_id = $request->drag_exit_cell_effect_id;
            $effect->drag_final_effect_id = $request->drag_final_effect_id;
            $effect->drag_stop_on_collision = (bool) $request->drag_stop_on_collision;
            $effect->drag_is_airborne = (bool) $request->drag_is_airborne;

            $effect->knockback_distance = (int) $request->knockback_distance;
            $effect->knockback_mover_type = (string) $request->knockback_mover_type;
            $effect->knockback_mover_speed = (float) $request->knockback_mover_speed;
            $effect->knockback_mover_height = (float) $request->knockback_mover_height;
            $effect->knockback_collide_with_environment_effect_id = $request->knockback_collide_with_environment_effect_id;
            $effect->knockback_collide_with_entities_effect_id = $request->knockback_collide_with_entities_effect_id;
            $effect->knockback_final_effect_id = $request->knockback_final_effect_id;

            $effect->chain_times = (int) $request->chain_times;
            $effect->chain_period = (float) $request->chain_period;
            $effect->chain_radius = (int) $request->chain_radius;
            $effect->chain_effect_id = $request->chain_effect_id;
            $effect->chain_final_effect_id = $request->chain_final_effect_id;

            $effect->move_animation = (string) $request->move_animation;
            $effect->move_speed = (float) $request->move_speed;
            $effect->move_distance = (int) $request->move_distance;
            $effect->move_random_direction = (bool) $request->move_random_direction;
            $effect->move_to_caster = (bool) $request->move_to_caster;
            $effect->move_to_target = (bool) $request->move_to_target;

            $effect->heal_base = (int) $request->heal_base;
            $effect->heal_flags = (array) $request->heal_flags;

            $effect->shield_base = (int) $request->shield_base;
            $effect->shield_behaviour_id = $request->shield_behaviour_id;

            $effect->damage_base = (int) $request->damage_base;
            $effect->damage_variance = (float) $request->damage_variance;
            $effect->damage_vampirism = (float) $request->damage_vampirism;
            $effect->damage_flags = (array) $request->damage_flags;
            $effect->damage_info_flags = (array) $request->damage_info_flags;
            $effect->damage_type = (string) $request->damage_type;
            $effect->damage_weapon_sound_type = (string) $request->damage_weapon_sound_type;

            $effect->per_behaviour_stack_damage_behaviour_id = $request->per_behaviour_stack_damage_behaviour_id;
            $effect->per_behaviour_stack_damage_subject = (string) $request->per_behaviour_stack_damage_subject;
            $effect->per_behaviour_stack_status_flags = (array) $request->per_behaviour_stack_status_flags;

            $effect->launch_missile_missile_id = $request->launch_missile_missile_id;
            $effect->launch_missile_caster_attachment_point = (string) $request->launch_missile_caster_attachment_point;
            $effect->launch_missile_target_attachment_point = (string) $request->launch_missile_target_attachment_point;
            $effect->launch_missile_mover_type = (string) $request->launch_missile_mover_type;
            $effect->launch_missile_mover_speed = (float) $request->launch_missile_mover_speed;
            $effect->launch_missile_mover_height = (float) $request->launch_missile_mover_height;
            $effect->launch_missile_mover_acceleration = (float) $request->launch_missile_mover_acceleration;
            $effect->launch_missile_mover_rotate = (bool) $request->launch_missile_mover_rotate;
            $effect->launch_missile_collide_with_environment_effect_id = $request->launch_missile_collide_with_environment_effect_id;
            $effect->launch_missile_collide_with_entities_effect_id = $request->launch_missile_collide_with_entities_effect_id;
            $effect->launch_missile_enter_cell_effect_id = $request->launch_missile_enter_cell_effect_id;
            $effect->launch_missile_exit_cell_effect_id = $request->launch_missile_exit_cell_effect_id;
            $effect->launch_missile_final_effect_id = $request->launch_missile_final_effect_id;
            $effect->launch_missile_caster_offset_x = (float) $request->launch_missile_caster_offset_x;
            $effect->launch_missile_caster_offset_y = (float) $request->launch_missile_caster_offset_y;
            $effect->launch_missile_caster_directional_offset_x = (float) $request->launch_missile_caster_directional_offset_x;
            $effect->launch_missile_caster_directional_offset_y = (float) $request->launch_missile_caster_directional_offset_y;
            $effect->launch_missile_target_offset_x = (float) $request->launch_missile_target_offset_x;
            $effect->launch_missile_target_offset_y = (float) $request->launch_missile_target_offset_y;
            $effect->launch_missile_target_directional_offset_x = (float) $request->launch_missile_target_directional_offset_x;
            $effect->launch_missile_target_directional_offset_y = (float) $request->launch_missile_target_directional_offset_y;
            $effect->launch_missile_fly_height = (float) $request->launch_missile_fly_height;
            $effect->launch_missile_finish_immediately = (bool) $request->launch_missile_finish_immediately;
            $effect->launch_missile_is_piercing = (bool) $request->launch_missile_is_piercing;

            $effect->launch_aoe_missile_radius = (int) $request->launch_aoe_missile_radius;
            $effect->launch_aoe_missile_effect_id = $request->launch_aoe_missile_effect_id;

            $effect->search_area_exclude_target = (bool) $request->search_area_exclude_target;
            $effect->search_area_exclude_origin = (bool) $request->search_area_exclude_origin;
            $effect->search_area_shape = (string) $request->search_area_shape;
            $effect->search_area_radius = (float) $request->search_area_radius;
            $effect->search_area_limit = (int) $request->search_area_limit;
            $effect->search_area_effect_id = $request->search_area_effect_id;
            $effect->search_area_check_line_of_sight = (bool) $request->search_area_check_line_of_sight;

            $effect->search_points_unoccupied = (bool) $request->search_points_unoccupied;

            $effect->search_dummies_range = (int) $request->search_dummies_range;
            $effect->search_dummies_limit = (int) $request->search_dummies_limit;
            $effect->search_dummies_effect_id = $request->search_dummies_effect_id;

            $effect->search_perimeter_effect_id = $request->search_perimeter_effect_id;
            $effect->search_perimeter_sides = (array) $request->search_perimeter_sides;
            $effect->search_perimeter_limit = (int) $request->search_perimeter_limit;
            $effect->search_perimeter_pick_random_side = (bool) $request->search_perimeter_pick_random_side;

            $effect->search_random_points_range_min = (int) $request->search_random_points_range_min;
            $effect->search_random_points_range_max = (int) $request->search_random_points_range_max;
            $effect->search_random_points_limit = (int) $request->search_random_points_limit;
            $effect->search_random_points_effect_id = $request->search_random_points_effect_id;

            $effect->search_behind_effect_id = $request->search_behind_effect_id;

            $effect->search_line_length = (float) $request->search_line_length;
            $effect->search_line_effect_id = $request->search_line_effect_id;
            $effect->search_line_exclude_origin = (bool) $request->search_line_exclude_origin;
            $effect->search_line_check_line_of_sight = (bool) $request->search_line_check_line_of_sight;

            $effect->apply_behaviour_behaviour_id = $request->apply_behaviour_behaviour_id;
            $effect->apply_behaviour_stacks = (int) $request->apply_behaviour_stacks;

            $effect->create_beam_path = (string) $request->create_beam_path;

            $effect->create_sfx_path = (string) $request->create_sfx_path;
            $effect->create_vfx_path = (string) $request->create_vfx_path;
            $effect->create_vfx_rotate = (bool) $request->create_vfx_rotate;

            $effect->create_unit_unit_id = $request->create_unit_unit_id;
            $effect->create_unit_owner = (string) $request->create_unit_owner;
            $effect->create_unit_duration = (int) $request->create_unit_duration;
            $effect->create_unit_health_fraction = (float) $request->create_unit_health_fraction;
            $effect->create_unit_kill_on_caster_death = (bool) $request->create_unit_kill_on_caster_death;
            $effect->create_unit_kill_on_episode_complete = (bool) $request->create_unit_kill_on_episode_complete;

            $effect->wait_seconds = (float) $request->wait_seconds;

            $effect->save();

            $effect->attachments()->delete();

            foreach ((array) $request->get('attachments', []) as $data) {
                $attachment = new Attachment();
                $attachment->point = (string) \array_get($data, 'point');
                $attachment->target = (string) \array_get($data, 'target');
                $attachment->prefab = (string) \array_get($data, 'prefab');
                $attachment->rotate = (bool) \array_get($data, 'rotate');
                $attachment->rotate_same_as_caster = (bool) \array_get($data, 'rotate_same_as_caster');
                $attachment->flip_rotation = (bool) \array_get($data, 'flip_rotation');
                $attachment->original_scale = (bool) \array_get($data, 'original_scale');
                $attachment->attachable_id = $effect->id;
                $attachment->attachable_type = __CLASS__;
                $attachment->save();
            }

            $effect->validators()->sync([]);

            foreach ((array) $request->get('validators', []) as $data) {
                $validatorId = (int) \array_get($data, 'pivot.validator_id');
                $validatorPurpose = (string) \array_get($data, 'pivot.validator_purpose');
                $effect->validators()->attach($validatorId, ['validator_purpose' => $validatorPurpose]);
            }

            $effect->effects()->sync([]);

            foreach ((array) $request->get('effects', []) as $data) {
                $effect->effects()->attach((int) \array_get($data, 'id'));
            }

            $effect->load($effect->with);
        });

        return $effect;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'effects';
        $this->timestamps = false;

        $this->with = [
            'validators',
            'effects',
            'attachments',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['heal_flags'] = 'array';
        $casts['damage_flags'] = 'array';
        $casts['damage_info_flags'] = 'array';
        $casts['dispell_behaviour_flags'] = 'array';
        $casts['dispell_behaviour_status_flags'] = 'array';
        $casts['search_perimeter_sides'] = 'array';
        $casts['per_behaviour_stack_status_flags'] = 'array';

        return $casts;
    }

    public function effects(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'effects_effects', 'parent_id', 'child_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable')->orderBy('id');
    }

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class, 'formula_id')->withDefault();
    }

    public function validators(): BelongsToMany
    {
        return $this->belongsToMany(Validator::class, 'effects_validators')
                    ->withPivot(['id', 'validator_purpose'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $data = [
            'Id'            => $this->id,
            'Name'          => (string) $this->name,
            'Type'          => (string) $this->type,
            'Target'        => (string) $this->target,
            'Sound'         => (string) $this->sound,
            'Animation'     => (string) $this->animation,
            'Chance'        => (float) $this->chance,
            'StackChance'   => (bool) $this->stack_chance,
            'CasterIsOwner' => (bool) $this->caster_is_owner,
            'Attachments'   => $this->attachments->present(),

            'Validators' => $this->validators->map(function (Validator $validator) {
                return [
                    'ValidatorId'      => $validator->id,
                    'ValidatorPurpose' => $validator->pivot->validator_purpose,
                ];
            })->toArray(),
        ];

        $data['Formula'] = $this->formula->getFormula($this->formula_text);

        if ($this->type === self::TYPE_DAMAGE || $this->type === self::TYPE_DAMAGE_WEAPON ||
            $this->type === self::TYPE_DAMAGE_RANDOM_ELEMENT || $this->type === self::TYPE_DAMAGE_PER_BEHAVIOUR_STACK) {

            $data['Base'] = (int) $this->damage_base;
            $data['Variance'] = (float) $this->damage_variance;
            $data['Vampirism'] = (float) $this->damage_vampirism;
            $data['DamageType'] = (string) $this->damage_type ? $this->damage_type : 'None';
            $data['WeaponSound'] = (string) $this->damage_weapon_sound_type ? $this->damage_weapon_sound_type : 'None';
            $data['DamageFlags'] = \implode(', ', $this->damage_flags ?: ['None']);
            $data['DamageInfoFlags'] = \implode(', ', $this->damage_info_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_DAMAGE_PER_BEHAVIOUR_STACK) {
            $data['BehaviourId'] = (int) $this->per_behaviour_stack_damage_behaviour_id;
            $data['Subject'] = $this->per_behaviour_stack_damage_subject ?: 'Victim';
            $data['StatusFlags'] = \implode(', ', $this->per_behaviour_stack_status_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_HEAL) {
            $data['Base'] = (int) $this->heal_base;
            $data['Flags'] = \implode(', ', $this->heal_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_DESTROY_EQUIPPED_ITEM) {
            $data['ItemId'] = (int) $this->destroy_equipped_item_id;
        }

        if ($this->type === self::TYPE_IF_ELSE) {
            $data['IfEffectId'] = (int) $this->if_else_if_effect_id;
            $data['ElseEffectId'] = (int) $this->if_else_else_effect_id;
        }

        if ($this->type === self::TYPE_SUCK_IN) {
            $data['Radius'] = (int) $this->suck_in_radius;
            $data['Duration'] = (float) $this->suck_in_duration;
            $data['Animation'] = (string) $this->suck_in_animation;
        }

        if ($this->type === self::TYPE_MOVE) {
            $data['Animation'] = (string) $this->move_animation;
            $data['Speed'] = (int) $this->move_speed;
            $data['Distance'] = (int) $this->move_distance;
        }

        if ($this->type === self::TYPE_SHIELD) {
            $data['BehaviourId'] = (int) $this->shield_behaviour_id;
            $data['Base'] = (int) $this->shield_base;
        }

        if ($this->type === self::TYPE_REWARD) {
            $data['RewardId'] = (int) $this->reward_id;
        }

        if ($this->type === self::TYPE_REPEAT) {
            $data['Times'] = (int) $this->repeat_times;
            $data['EffectId'] = (int) $this->repeat_effect_id;
        }

        if ($this->type === self::TYPE_CHAIN) {
            $data['Times'] = (int) $this->chain_times;
            $data['Period'] = (float) $this->chain_period;
            $data['Radius'] = (int) $this->chain_radius;
            $data['EffectId'] = (int) $this->chain_effect_id;
            $data['FinalEffectId'] = (int) $this->chain_final_effect_id;
        }

        if ($this->type === self::TYPE_RESTORE_RESOURCE) {
            $data['ResourceType'] = (string) $this->restore_resource_type;
            $data['ResourceAmount'] = (string) $this->restore_resource_amount;
        }

        if ($this->type === self::TYPE_DRAG) {
            $data['StartAnimation'] = (string) $this->drag_start_animation;
            $data['EndAnimation'] = (string) $this->drag_end_animation;
            $data['Mover'] = [
                'Type'   => (string) $this->drag_mover_type,
                'Speed'  => (float) $this->drag_mover_speed,
                'Height' => (float) $this->drag_mover_height,
            ];
            $data['CollideWithEnvironmentEffectId'] = (int) $this->drag_collide_with_environment_effect_id;
            $data['CollideWithEntityEffectId'] = (int) $this->drag_collide_with_entities_effect_id;
            $data['EnterCellEffectId'] = (int) $this->drag_enter_cell_effect_id;
            $data['ExitCellEffectId'] = (int) $this->drag_exit_cell_effect_id;
            $data['FinalEffectId'] = (int) $this->drag_final_effect_id;
            $data['StopOnCollision'] = (bool) $this->drag_stop_on_collision;
            $data['IsAirborne'] = (bool) $this->drag_is_airborne;
        }

        if ($this->type === self::TYPE_RUN_AWAY) {
            $data['Animation'] = (string) $this->move_animation;
            $data['Speed'] = (float) $this->move_speed;
            $data['Distance'] = (int) $this->move_distance;
            $data['RandomDirection'] = (bool) $this->move_random_direction;
            $data['ToCaster'] = (bool) $this->move_to_caster;
            $data['ToTarget'] = (bool) $this->move_to_target;
        }

        if ($this->type === self::TYPE_ADD_CURRENCY) {
            $data['CurrencyType'] = (string) $this->currency_type;
            $data['CurrencyFormula'] = (string) $this->currency_formula;
        }

        if ($this->type === self::TYPE_HOOK) {
            $data['Hook'] = (string) $this->hook_hook;
            $data['Beam'] = (string) $this->hook_beam;
        }

        if ($this->type === self::TYPE_LAUNCH_MISSILE ||
            $this->type === self::TYPE_LAUNCH_MISSILE_FROM_TARGET ||
            $this->type === self::TYPE_LAUNCH_AOE_MISSILE ||
            $this->type === self::TYPE_LAUNCH_WEAPON_MISSILE) {

            $data['MissileId'] = (int) $this->launch_missile_missile_id;
            $data['CasterAttachmentPoint'] = (string) $this->launch_missile_caster_attachment_point ?: 'None';
            $data['TargetAttachmentPoint'] = (string) $this->launch_missile_target_attachment_point ?: 'None';
            $data['Mover'] = [
                'Type'         => (string) $this->launch_missile_mover_type,
                'Speed'        => (float) $this->launch_missile_mover_speed,
                'Height'       => (float) $this->launch_missile_mover_height,
                'Acceleration' => (float) $this->launch_missile_mover_acceleration,
                'Rotate'       => (bool) $this->launch_missile_mover_rotate,
            ];
            $data['CollideWithEnvironmentEffectId'] = (int) $this->launch_missile_collide_with_environment_effect_id;
            $data['CollideWithEntitiesEffectId'] = (int) $this->launch_missile_collide_with_entities_effect_id;
            $data['EnterCellEffectId'] = (int) $this->launch_missile_enter_cell_effect_id;
            $data['ExitCellEffectId'] = (int) $this->launch_missile_exit_cell_effect_id;
            $data['FinalEffectId'] = (int) $this->launch_missile_final_effect_id;
            $data['FinishImmediately'] = (bool) $this->launch_missile_finish_immediately;
            $data['IsPiercing'] = (bool) $this->launch_missile_is_piercing;
            $data['CasterOffsetX'] = (float) $this->launch_missile_caster_offset_x;
            $data['CasterOffsetY'] = (float) $this->launch_missile_caster_offset_y;
            $data['CasterDirectionalOffsetX'] = (float) $this->launch_missile_caster_directional_offset_x;
            $data['CasterDirectionalOffsetY'] = (float) $this->launch_missile_caster_directional_offset_y;
            $data['TargetOffsetX'] = (float) $this->launch_missile_target_offset_x;
            $data['TargetOffsetY'] = (float) $this->launch_missile_target_offset_y;
            $data['TargetDirectionalOffsetX'] = (float) $this->launch_missile_target_directional_offset_x;
            $data['TargetDirectionalOffsetY'] = (float) $this->launch_missile_target_directional_offset_y;
            $data['MissileFlyHeight'] = (float) $this->launch_missile_fly_height;

            if ($this->type === self::TYPE_LAUNCH_AOE_MISSILE) {
                $data['EffectId'] = (int) $this->launch_aoe_missile_effect_id;
                $data['Radius'] = (int) $this->launch_aoe_missile_radius;
            }
        }

        if ($this->type === self::TYPE_MIRROR_IMAGE) {
            $data['BehaviourId'] = (int) $this->mirror_image_behaviour_id;
            $data['Prefab'] = (string) $this->mirror_image_prefab;
            $data['Duration'] = (int) $this->mirror_image_duration;
            $data['Count'] = (int) $this->mirror_image_count;
        }

        if ($this->type === self::TYPE_SEARCH_PERIMETER) {
            $data['EffectId'] = (int) $this->search_perimeter_effect_id;
            $data['Sides'] = implode(', ', $this->search_perimeter_sides ?: ['None']);
            $data['Limit'] = (int) $this->search_perimeter_limit;
            $data['PickRandomSide'] = (bool) $this->search_perimeter_pick_random_side;
        }

        if ($this->type === self::TYPE_SEARCH_DUMMIES) {
            $data['Range'] = (int) $this->search_dummies_range;
            $data['Limit'] = (int) $this->search_dummies_limit;
            $data['EffectId'] = (int) $this->search_dummies_effect_id;
        }

        if ($this->type === self::TYPE_KNOCKBACK) {
            $data['Distance'] = (int) $this->knockback_distance;
            $data['Mover'] = [
                'Type'   => (string) $this->knockback_mover_type,
                'Speed'  => (float) $this->knockback_mover_speed,
                'Height' => (float) $this->knockback_mover_height,
            ];
            $data['CollideWithEnvironmentEffectId'] = (int) $this->knockback_collide_with_environment_effect_id;
            $data['CollideWithEntityEffectId'] = (int) $this->knockback_collide_with_entities_effect_id;
            $data['FinalEffectId'] = (int) $this->knockback_final_effect_id;
        }

        if ($this->type === self::TYPE_SEARCH_AREA || $this->type === self::TYPE_SEARCH_POINTS || $this->type === self::TYPE_SEARCH_CORPSES) {
            $data['Shape'] = (string) $this->search_area_shape;
            $data['Radius'] = (float) $this->search_area_radius;
            $data['Limit'] = (int) $this->search_area_limit;
            $data['EffectId'] = (int) $this->search_area_effect_id;
            $data['ExcludeOrigin'] = (bool) $this->search_area_exclude_origin;
            $data['ExcludeTarget'] = (bool) $this->search_area_exclude_target;
            $data['CheckLineOfSight'] = (bool) $this->search_area_check_line_of_sight;
        }

        if ($this->type === self::TYPE_SEARCH_POINTS) {
            $data['Unoccupied'] = (bool) $this->search_points_unoccupied;
        }

        if ($this->type === self::TYPE_SEARCH_BEHIND) {
            $data['EffectId'] = (int) $this->search_behind_effect_id;
        }

        if ($this->type === self::TYPE_SEARCH_LINE) {
            $data['Length'] = (int) $this->search_line_length;
            $data['EffectId'] = (int) $this->search_line_effect_id;
            $data['ExcludeOrigin'] = (bool) $this->search_line_exclude_origin;
            $data['CheckLineOfSight'] = (bool) $this->search_line_check_line_of_sight;
        }

        if ($this->type === self::TYPE_SEARCH_RANDOM_POINTS) {
            $data['RangeMin'] = (int) $this->search_random_points_range_min;
            $data['RangeMax'] = (int) $this->search_random_points_range_max;
            $data['Limit'] = (int) $this->search_random_points_limit;
            $data['EffectId'] = (int) $this->search_random_points_effect_id;
        }

        if ($this->type === self::TYPE_APPLY_BEHAVIOUR) {
            $data['BehaviourId'] = (int) $this->apply_behaviour_behaviour_id;
            $data['Stacks'] = (int) $this->apply_behaviour_stacks;
        }

        if ($this->type === self::TYPE_REMOVE_BEHAVIOUR) {
            $data['BehaviourId'] = (int) $this->remove_behaviour_behaviour_id;
        }

        if ($this->type === self::TYPE_HEAL_FROM_TARGET_HEALTH) {
            $data['Fraction'] = (float) $this->heal_from_target_health_fraction;
        }

        if ($this->type === self::TYPE_DISPEL) {
            $data['BehaviourFlags'] = \implode(', ', $this->dispell_behaviour_flags ?: ['None']);
            $data['BehaviourStatusFlags'] = \implode(', ', $this->dispell_behaviour_status_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_CREATE_BEAM) {
            $data['Path'] = (string) $this->create_beam_path;
        }

        if ($this->type === self::TYPE_CREATE_SOUND) {
            $data['Path'] = (string) $this->create_sfx_path;
        }

        if ($this->type === self::TYPE_REDUCE_COOLDOWNS) {
            $data['Amount'] = (int) $this->reduce_cooldowns_amount;
        }

        if ($this->type === self::TYPE_RUN_COOLDOWN) {
            $data['SkillId'] = (int) $this->run_cooldown_skill_id;
        }

        if ($this->type === self::TYPE_CREATE_UNIT) {
            $data['UnitId'] = (int) $this->create_unit_unit_id;
            $data['Duration'] = (int) $this->create_unit_duration;
            $data['HealthFraction'] = (float) $this->create_unit_health_fraction;
            $data['Owner'] = (string) $this->create_unit_owner ?: 'Auto';
            $data['KillOnCasterDeath'] = (bool) $this->create_unit_kill_on_caster_death;
            $data['KillOnEpisodeComplete'] = (bool) $this->create_unit_kill_on_episode_complete;
        }

        if ($this->type === self::TYPE_WAIT) {
            $data['Seconds'] = (float) $this->wait_seconds;
        }

        if ($this->type === self::TYPE_RANDOM_WAIT) {
            $data['Min'] = (float) $this->random_wait_min;
            $data['Max'] = (float) $this->random_wait_max;
        }

        if ($this->type === self::TYPE_SET || $this->type === self::TYPE_RANDOM || $this->type === self::TYPE_DAMAGE || $this->type === self::TYPE_DAMAGE_WEAPON) {
            $data['Effects'] = $this->effects->map(function (Effect $effect) {
                return $effect->id;
            })->toArray();
        }

        return $data;
    }
}
