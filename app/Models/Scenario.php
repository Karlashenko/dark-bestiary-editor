<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\ScenarioRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class Scenario
 *
 * @package App\Models
 */
class Scenario extends Model implements Presentable
{
    public const TYPES = [
        'Campaign',
        'Patrol',
        'Nightmare',
        'Adventure',
    ];

    /**
     * @param Scenario        $scenario
     * @param ScenarioRequest $request
     *
     * @return Scenario
     * @throws Throwable
     */
    public static function populate(Scenario $scenario, ScenarioRequest $request): self
    {
        DB::transaction(function () use ($scenario, $request) {
            $scenario->index = (int) $request->index;
            $scenario->monster_level_min = (int) $request->monster_level_min;
            $scenario->monster_level_max = (int) $request->monster_level_max;
            $scenario->type = (string) $request->type;
            $scenario->name_i18n_id = $request->name_i18n_id;
            $scenario->description_i18n_id = $request->description_i18n_id;
            $scenario->commentary_i18n_id = $request->commentary_i18n_id;
            $scenario->complete_i18n_id = $request->complete_i18n_id;
            $scenario->position_x = (float) $request->position_x;
            $scenario->position_y = (float) $request->position_y;
            $scenario->reward_experience = (int) $request->reward_experience;
            $scenario->reward_gold = (int) $request->reward_gold;
            $scenario->is_unlocked = (bool) $request->is_unlocked;
            $scenario->is_start = (bool) $request->is_start;
            $scenario->is_end = (bool) $request->is_end;
            $scenario->is_disposable = (bool) $request->is_disposable;
            $scenario->is_ascension = (bool) $request->is_ascension;
            $scenario->is_hidden = (bool) $request->is_hidden;
            $scenario->save();

            $scenario->items()->sync([]);

            foreach ((array) $request->get('items', []) as $item) {
                $scenario->items()->attach(
                    (int) array_get($item, 'id'),
                    [
                        'is_choosable' => (bool) array_get($item, 'pivot.is_choosable'),
                        'stack_count' => (int) array_get($item, 'pivot.stack_count'),
                    ]
                );
            }

            $scenario->children()->sync([]);

            foreach ((array) $request->get('children', []) as $data) {
                $scenario->children()->attach((int) array_get($data, 'id'));
            }

            foreach ($scenario->episodes as $episode) {
                $episode->encounter->unitTable->delete();
                $episode->encounter->delete();
                $episode->delete();
            }

            foreach ((array) $request->get('episodes', []) as $episodeData) {
                $table = new UnitTable();
                $table->count = (int) array_get($episodeData, 'encounter.unit_table.count');
                $table->save();

                foreach ((array) array_get($episodeData, 'encounter.unit_table.units') as $unitData) {
                    $unit = new UnitTableUnit();
                    $unit->table_id = $table->id;
                    $unit->unit_id = (int) array_get($unitData, 'unit_id');
                    $unit->probability = (float) array_get($unitData, 'probability');
                    $unit->is_guaranteed = (bool) array_get($unitData, 'is_guaranteed');
                    $unit->is_unique = (bool) array_get($unitData, 'is_unique');
                    $unit->is_enabled = (bool) array_get($unitData, 'is_enabled');
                    $unit->save();
                }

                $encounter = new Encounter();
                $encounter->type = (string) (array_get($episodeData, 'encounter.type') ?? Encounter::TYPES[1]);
                $encounter->unit_source_type = (string) (array_get($episodeData, 'encounter.unit_source_type') ?? Encounter::UNIT_SOURCE_TYPES[1]);
                $encounter->unit_group_challenge_rating = (int) array_get($episodeData, 'encounter.unit_group_challenge_rating');
                $encounter->unit_group_environment_id = array_get($episodeData, 'encounter.unit_group_environment_id');
                $encounter->unit_table_id = $table->id;
                $encounter->start_phrase_id = array_get($episodeData, 'encounter.start_phrase_id');
                $encounter->complete_phrase_id = array_get($episodeData, 'encounter.complete_phrase_id');
                $encounter->save();

                $encounter->unitGroups()
                          ->sync((new Collection(array_get($episodeData, 'encounter.unit_groups', [])))->pluck('id')->toArray());

                $encounter->phrases()
                          ->sync((new Collection(array_get($episodeData, 'encounter.phrases', [])))->pluck('id')->toArray());

                $episode = new Episode();
                $episode->label = (string) array_get($episodeData, 'label');
                $episode->environment_id = array_get($episodeData, 'environment_id');
                $episode->scenario_id = $scenario->id;
                $episode->encounter_id = $encounter->id;
                $episode->save();

                $episode->scenes()
                        ->sync((new Collection(array_get($episodeData, 'scenes', [])))->pluck('id')->toArray());
            }
        });

        $scenario->load($scenario->with);

        return $scenario;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'scenarios';
        $this->timestamps = false;
        $this->with = ['items', 'episodes', 'nameI18n', 'descriptionI18n', 'completeI18n', 'children', 'environment'];
    }

    /**
     * @return BelongsToMany
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'scenarios_scenarios', 'parent_id', 'child_id')
                    ->without('children')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'scenarios_scenarios', 'child_id', 'parent_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'scenarios_items')
                    ->withPivot(['is_choosable', 'stack_count'])
                    ->orderBy('is_choosable');
    }

    /**
     * @return HasMany
     */
    public function episodes(): HasMany
    {
        return $this->hasMany(Episode::class, 'scenario_id')->orderBy('id');
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
    public function completeI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'complete_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class, 'environment_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function descriptionI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function commentaryI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'commentary_i18n_id')->withDefault();
    }

    /**
     * @return array
     */
    public function present(): array
    {
        return [
            'Id'              => $this->id,
            'Index'           => $this->index,
            'NameKey'         => $this->nameI18n->key,
            'DescriptionKey'  => $this->descriptionI18n->key,
            'CommentaryKey'   => $this->commentaryI18n->key,
            'CompleteKey'     => $this->completeI18n->key,
            'MinMonsterLevel' => (int) $this->monster_level_min,
            'MaxMonsterLevel' => (int) $this->monster_level_max,
            'Type'            => (string) $this->type,
            'IsUnlocked'      => (bool) $this->is_unlocked,
            'IsStart'         => (bool) $this->is_start,
            'IsEnd'           => (bool) $this->is_end,
            'IsHidden'        => (bool) $this->is_hidden,
            'IsDisposable'    => (bool) $this->is_disposable,
            'IsAscension'     => (bool) $this->is_ascension,
            'PositionX'       => (float) $this->position_x,
            'PositionY'       => (float) $this->position_y,
            'Gold'            => (int) $this->reward_gold,
            'Experience'      => (int) $this->reward_experience,
            'Episodes'        => $this->episodes->present()->toArray(),
            'Children'        => $this->children->map(function (Scenario $scenario) { return $scenario->id; }),
            'Rewards'         => $this->items->map(function (Item $item) {
                return [
                    'ItemId'      => $item->id,
                    'IsChoosable' => (bool) $item->pivot->is_choosable,
                    'StackCount'  => (int) $item->pivot->stack_count,
                ];
            }),
        ];
    }
}
