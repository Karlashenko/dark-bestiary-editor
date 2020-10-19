<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\AchievementRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * Class Achievement
 *
 * @package App\Models
 */
class Achievement extends Model implements Presentable
{
    public const TYPES = [
        'KillAchievement',
        'CollectAchievement',
        'CompleteScenarioAchievement',
        'LevelupAchievement',
        'CompleteVisionsAchievement',
    ];

    /**
     * @param Achievement        $achievement
     * @param AchievementRequest $request
     *
     * @return Achievement
     * @throws \Throwable
     * @throws \Exception
     */
    public static function populate(Achievement $achievement, AchievementRequest $request): self
    {
        DB::transaction(function () use ($achievement, $request) {
            $achievement->is_enabled = (bool) $request->is_enabled;
            $achievement->index = (int) $request->index;
            $achievement->steam_api_key = (string) $request->steam_api_key;
            $achievement->type = (string) $request->type;
            $achievement->icon = (string) $request->icon;
            $achievement->required_quantity = (int) $request->required_quantity;
            $achievement->level = (int) $request->level;
            $achievement->points = (int) $request->points;
            $achievement->name_i18n_id = $request->name_i18n_id;
            $achievement->description_i18n_id = $request->description_i18n_id;
            $achievement->unit_id = $request->unit_id;
            $achievement->item_id = $request->item_id;
            $achievement->scenario_id = $request->scenario_id;
            $achievement->save();

            $achievement->children()->sync([]);

            foreach ((array) $request->get('children', []) as $data) {
                $achievement->children()->attach((int) \array_get($data, 'id'));
            }

            $achievement->conditions()->sync([]);

            foreach ((array) $request->get('conditions', []) as $data) {
                $achievement->conditions()->attach((int) \array_get($data, 'id'));
            }

            $achievement->load($achievement->with);
        });

        return $achievement;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'children', 'conditions'];
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function descriptionI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'achievements_achievements', 'parent_id', 'child_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function conditions(): BelongsToMany
    {
        return $this->belongsToMany(AchievementCondition::class, 'achievements_achievement_conditions')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'achievements_achievements', 'child_id', 'parent_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'               => $this->id,
            'Index'            => $this->index,
            'IsEnabled'        => $this->is_enabled,
            'NameKey'          => $this->namei18n->key,
            'DescriptionKey'   => $this->descriptioni18n->key,
            'SteamApiKey'      => $this->steam_api_key,
            'Type'             => $this->type,
            'Icon'             => $this->icon,
            'Level'            => (int) $this->level,
            'Points'           => (int) $this->points,
            'RequiredQuantity' => (int) $this->required_quantity,
            'UnitId'           => (int) $this->unit_id,
            'ItemId'           => (int) $this->item_id,
            'ScenarioId'       => (int) $this->scenario_id,
            'Conditions'       => $this->conditions->present(),
        ];
    }

}
