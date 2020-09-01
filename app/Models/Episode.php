<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Episode
 *
 * @package App\Models
 */
class Episode extends Model implements Presentable
{
    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'episodes';
        $this->timestamps = false;
        $this->with = ['scenes', 'encounter'];
    }

    /**
     * @return BelongsToMany
     */
    public function scenes(): BelongsToMany
    {
        return $this->belongsToMany(Scene::class, 'episodes_scenes')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsTo
     */
    public function encounter(): BelongsTo
    {
        return $this->belongsTo(Encounter::class, 'encounter_id')->withDefault(['unit_table' => new UnitTable(['units' => []])]);
    }

    /**
     * @return HasOne
     */
    public function scenario(): HasOne
    {
        return $this->hasOne(Scenario::class, 'scenario_id');
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Scenes'        => $this->scenes->pluck('id')->toArray(),
            'Encounter'     => $this->encounter->present(),
            'EnvironmentId' => (int) $this->environment_id,
        ];
    }
}
