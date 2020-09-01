<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\ValidatorRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Validator
 *
 * @package App\Models
 */
class Validator extends Model implements Presentable
{
    public const TYPE_SCENARIO_IS_COMPLETED       = 'ScenarioIsCompletedValidator';
    public const TYPE_CASTER_HAVE_STATUS_FLAG     = 'CasterHaveStatusFlagValidator';
    public const TYPE_CASTER_HAVE_NOT_STATUS_FLAG = 'CasterHaveNotStatusFlagValidator';
    public const TYPE_TARGET_HAVE_STATUS_FLAG     = 'TargetHaveStatusFlagValidator';
    public const TYPE_TARGET_HAVE_NOT_STATUS_FLAG = 'TargetHaveNotStatusFlagValidator';
    public const TYPE_TARGET_CHALLENGE_RATING     = 'TargetChallengeRatingValidator';
    public const TYPE_TARGET_ACTION_POINTS        = 'TargetActionPointsValidator';
    public const TYPE_TARGET_IS_ENEMY             = 'TargetIsEnemyValidator';
    public const TYPE_TARGET_IN_RANGE             = 'TargetInRangeValidator';
    public const TYPE_TARGET_IS_ALLY              = 'TargetIsAllyValidator';
    public const TYPE_TARGET_IS_UNIT_OF_TYPE      = 'TargetIsUnitOfTypeValidator';
    public const TYPE_TARGET_IS_NOT_UNIT_OF_TYPE  = 'TargetIsNotUnitOfTypeValidator';
    public const TYPE_TARGET_BEHAVIOUR_COUNT      = 'TargetBehaviourCountValidator';
    public const TYPE_CASTER_BEHAVIOUR_COUNT      = 'CasterBehaviourCountValidator';
    public const TYPE_UNITS_WITH_BEHAVIOUR_COUNT  = 'UnitsWithBehaviourValidator';
    public const TYPE_TARGET_UNIT_HAVE_FLAGS      = 'TargetUnitHaveFlagsValidator';
    public const TYPE_TARGET_UNIT_HAVE_NOT_FLAGS  = 'TargetUnitHaveNotFlagsValidator';
    public const TYPE_TARGET_HEALTH_FRACTION      = 'TargetHealthFractionValidator';
    public const TYPE_UNIT_COUNT                  = 'UnitCountValidator';
    public const TYPE_COMBINE_OR                  = 'CombineOrValidator';
    public const TYPE_COMBINE_AND                 = 'CombineAndValidator';

    public const TYPES = [
        self::TYPE_SCENARIO_IS_COMPLETED,
        self::TYPE_CASTER_HAVE_STATUS_FLAG,
        self::TYPE_CASTER_HAVE_NOT_STATUS_FLAG,
        self::TYPE_TARGET_HAVE_STATUS_FLAG,
        self::TYPE_TARGET_HAVE_NOT_STATUS_FLAG,
        self::TYPE_TARGET_CHALLENGE_RATING,
        self::TYPE_TARGET_ACTION_POINTS,
        self::TYPE_TARGET_IS_ENEMY,
        self::TYPE_TARGET_IN_RANGE,
        self::TYPE_TARGET_IS_ALLY,
        self::TYPE_TARGET_IS_UNIT_OF_TYPE,
        self::TYPE_TARGET_IS_NOT_UNIT_OF_TYPE,
        self::TYPE_UNITS_WITH_BEHAVIOUR_COUNT,
        self::TYPE_TARGET_BEHAVIOUR_COUNT,
        self::TYPE_CASTER_BEHAVIOUR_COUNT,
        self::TYPE_TARGET_UNIT_HAVE_FLAGS,
        self::TYPE_TARGET_UNIT_HAVE_NOT_FLAGS,
        self::TYPE_TARGET_HEALTH_FRACTION,
        self::TYPE_UNIT_COUNT,
        self::TYPE_COMBINE_OR,
        self::TYPE_COMBINE_AND,
        'TargetHasUsedSkillThisRound',
        'TargetHasOverhealValidator',
        'TargetIsVisibleValidator',
    ];

    public const PURPOSE_NONE  = 'None';
    public const PURPOSE_APPLY = 'Apply';
    public const PURPOSE_OTHER = 'Other';

    public const PURPOSES = [
        self::PURPOSE_NONE,
        self::PURPOSE_APPLY,
        self::PURPOSE_OTHER,
    ];

    public const COMPARATORS = [
        'EqualTo',
        'NotEqualTo',
        'GreaterThan',
        'GreaterThanOrEqualTo',
        'LessThan',
        'LessThanOrEqualTo',
    ];

    /**
     * @param Validator        $validator
     * @param ValidatorRequest $request
     *
     * @return Validator
     * @throws \Throwable
     * @throws \Exception
     */
    public static function populate(Validator $validator, ValidatorRequest $request): Validator
    {
        DB::transaction(function () use ($validator, $request) {
            $validator->name = (string) $request->name;
            $validator->type = (string) $request->type;
            $validator->comparator = (string) $request->comparator;
            $validator->value = (int) $request->value;
            $validator->fraction = (float) $request->fraction;
            $validator->behaviour_id = $request->behaviour_id;
            $validator->unit_flags = (array) $request->unit_flags;
            $validator->status_flags = (array) $request->status_flags;
            $validator->unit_id = $request->unit_id;
            $validator->scenario_id = $request->scenario_id;
            $validator->range_min = (float) $request->range_min;
            $validator->range_max = (float) $request->range_max;
            $validator->save();

            $validator->validators()->sync([]);

            foreach ((array) $request->get('validators', []) as $data) {
                $id = (int) \array_get($data, 'id');

                if ($id === 0) {
                    continue;
                }

                $validator->validators()->attach($id);
            }

            $validator->load($validator->with);
        });

        return $validator;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'validators';
        $this->with = ['validators'];
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function getCasts(): array
    {
        $casts = parent::getCasts();
        $casts['unit_flags'] = 'array';
        $casts['status_flags'] = 'array';

        return $casts;
    }

    /**
     * @return BelongsToMany
     */
    public function validators(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'validators_validators', 'parent_id', 'child_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        $presentation = [
            'Id'   => $this->id,
            'Type' => $this->type,
        ];

        if ($this->type === self::TYPE_TARGET_BEHAVIOUR_COUNT ||
            $this->type === self::TYPE_CASTER_BEHAVIOUR_COUNT ||
            $this->type === self::TYPE_UNITS_WITH_BEHAVIOUR_COUNT) {
            $presentation['BehaviourId'] = (int) $this->behaviour_id;
            $presentation['Comparator'] = $this->comparator;
            $presentation['Value'] = $this->value;
        }

        if ($this->type === self::TYPE_TARGET_CHALLENGE_RATING ||
            $this->type === self::TYPE_TARGET_ACTION_POINTS) {
            $presentation['Comparator'] = $this->comparator;
            $presentation['Value'] = $this->value;
        }

        if ($this->type === self::TYPE_SCENARIO_IS_COMPLETED) {
            $presentation['ScenarioId'] = (int) $this->scenario_id;
        }

        if ($this->type === self::TYPE_UNIT_COUNT) {
            $presentation['UnitId'] = (int) $this->unit_id;
            $presentation['Comparator'] = $this->comparator;
            $presentation['Value'] = $this->value;
        }

        if ($this->type === self::TYPE_TARGET_HEALTH_FRACTION) {
            $presentation['Comparator'] = $this->comparator;
            $presentation['Fraction'] = $this->fraction;
        }

        if ($this->type === self::TYPE_CASTER_HAVE_STATUS_FLAG ||
            $this->type === self::TYPE_CASTER_HAVE_NOT_STATUS_FLAG ||
            $this->type === self::TYPE_TARGET_HAVE_STATUS_FLAG ||
            $this->type === self::TYPE_TARGET_HAVE_NOT_STATUS_FLAG) {
            $presentation['Flags'] = \implode(', ', $this->status_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_TARGET_UNIT_HAVE_FLAGS ||
            $this->type === self::TYPE_TARGET_UNIT_HAVE_NOT_FLAGS) {
            $presentation['Flags'] = \implode(', ', $this->unit_flags ?: ['None']);
        }

        if ($this->type === self::TYPE_TARGET_IN_RANGE) {
            $presentation['Min'] = (float) $this->range_min;
            $presentation['Max'] = (float) $this->range_max;
        }

        if ($this->type === self::TYPE_TARGET_IS_UNIT_OF_TYPE ||
            $this->type === self::TYPE_TARGET_IS_NOT_UNIT_OF_TYPE) {
            $presentation['UnitId'] = (int) $this->unit_id;
        }

        if ($this->type === self::TYPE_COMBINE_OR || $this->type === self::TYPE_COMBINE_AND) {
            $presentation['Validators'] = $this->validators->map(function (Validator $validator) {
                return $validator->id;
            })->toArray();
        }

        return $presentation;
    }
}
