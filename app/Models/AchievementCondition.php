<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class AchievementCondition extends Model implements Presentable
{
    public const TYPES = [
        'KillAchievementCondition',
        'ReceiveBehaviourAchievementCondition',
        'EnemySuicideAchievementCondition',
    ];

    /**
     * @param AchievementCondition $condition
     * @param Request              $request
     *
     * @return AchievementCondition
     */
    public static function populate(AchievementCondition $condition, Request $request): self
    {
        $condition->label = (string) $request->label;
        $condition->type = (string) $request->type;
        $condition->comparator = (string) $request->comparator;
        $condition->required_quantity = (int) $request->required_quantity;
        $condition->unit_id = $request->unit_id;
        $condition->behaviour_id = $request->behaviour_id;
        $condition->save();

        return $condition;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'achievement_conditions';
        $this->timestamps = false;
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Type'             => (string) $this->type,
            'Comparator'       => (string) $this->comparator,
            'RequiredQuantity' => (int) $this->required_quantity,
            'UnitId'           => (int) $this->unit_id,
            'BehaviourId'      => (int) $this->behaviour_id,
        ];
    }

}
