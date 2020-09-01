<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\UnitRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Unit
 *
 * @package App\Models
 */
class Unit extends Model implements Presentable
{
    public const FLAGS = [
        'Summoned',
        'Playable',
        'Immovable',
        'BlocksMovement',
        'BlocksVision',
        'Dummy',
        'Beast',
        'Insect',
        'Corpseless',
        'Humanoid',
        'Magical',
        'Ethereal',
        'Ooze',
        'Plant',
        'Wooden',
        'Undead',
        'Demon',
        'Structure',
        'Stone',
        'Mechanical',
        'Airborne',
        'Boss',
        'CampaignOnly',
    ];

    public const ARMOR_SOUND_TYPES = [
        'None',
        'Flesh',
        'Metal',
        'Wood',
    ];

    /**
     * @param Unit        $unit
     * @param UnitRequest $request
     *
     * @return Unit
     * @throws \Exception
     * @throws \Throwable
     */
    public static function populate(Unit $unit, UnitRequest $request): self
    {
        DB::transaction(function () use ($unit, $request) {
            $unit->label = (string) $request->label;
            $unit->suffix = (string) $request->suffix;
            $unit->name_i18n_id = $request->name_i18n_id;
            $unit->description_i18n_id = $request->description_i18n_id;
            $unit->model = (string) $request->model;
            $unit->armor_sound_type = (string) $request->armor_sound_type;
            $unit->environment_id = $request->environment_id;
            $unit->archetype_id = $request->archetype_id;
            $unit->behaviour_tree_id = $request->behaviour_tree_id;
            $unit->challenge_rating = (int) $request->challenge_rating;
            $unit->mover_type = (string) $request->mover_type;
            $unit->mover_speed = (float) $request->mover_speed;
            $unit->mover_height = (float) $request->mover_height;
            $unit->flags = (array) $request->flags;
            $unit->save();

            $unit->behaviours()->sync([]);

            foreach ((array) $request->get('behaviours', []) as $behaviour) {
                $unit->behaviours()->attach((int) \array_get($behaviour, 'id'));
            }

            $unit->items()->sync([]);

            foreach ((array) $request->get('items', []) as $item) {
                $unit->items()->attach((int) \array_get($item, 'id'));
            }

            $unit->skills()->sync([]);

            foreach ((array) $request->get('skills', []) as $skill) {
                $id = (int) \array_get($skill, 'id');
                $unit->skills()->attach($id);
            }

            $unit->loot()->sync([]);

            foreach ((array) $request->get('loot', []) as $loot) {
                $id = (int) \array_get($loot, 'id');
                $unit->loot()->attach($id);
            }

            $unit->files()->sync([]);

            foreach ((array) $request->get('files', []) as $file) {
                $unit->files()->attach($file['id']);
            }

            $unit->load($unit->with);
        });

        return $unit;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'units';
        $this->timestamps = false;

        $this->with = [
            'loot',
            'skills',
            'nameI18n',
            'descriptionI18n',
            'files',
            'items',
            'behaviours',
            'environment',
            'archetype',
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

    /**
     * @return BelongsToMany
     */
    public function behaviours(): BelongsToMany
    {
        return $this->belongsToMany(Behaviour::class, 'units_behaviours')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'units_items');
    }

    /**
     * @return BelongsTo
     */
    public function environment(): BelongsTo
    {
        return $this->belongsTo(Environment::class, 'environment_id')->withDefault();
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'units_skills')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function loot(): BelongsToMany
    {
        return $this->belongsToMany(Loot::class, 'units_loot');
    }

    /**
     * @return MorphToMany
     */
    public function files(): MorphToMany
    {
        return $this->morphToMany(File::class, 'fileable');
    }

    /**
     * @return BelongsTo
     */
    public function archetype() : BelongsTo
    {
        return $this->belongsTo(Archetype::class, 'archetype_id')->withDefault();
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
    public function descriptionI18n() : BelongsTo
    {
        return $this->belongsTo(I18N::class, 'description_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'               => $this->id,
            'Label'            => $this->label,
            'NameKey'          => $this->nameI18n->key,
            'DescriptionKey'   => $this->descriptionI18n->key,
            'Flags'            => \implode(', ', $this->flags ?: ['None']),
            'ArmorSound'       => $this->armor_sound_type ?: 'None',
            'Model'            => $this->model,
            'ChallengeRating'  => $this->challenge_rating,
            'ArchetypeId'      => (int) $this->archetype_id,
            'Environment'      => $this->environment->present(),
            'BehaviourTreeId'  => (int) $this->behaviour_tree_id,
            'Skills'           => $this->skills->pluck('id')->toArray(),
            'Loot'             => $this->loot->pluck('id')->toArray(),
            'Behaviours'       => $this->behaviours->pluck('id')->toArray(),
            'Equipment'        => $this->items->pluck('id')->toArray(),
            'Environment'      => $this->environment ? $this->environment->present() : null,

            'Mover' => [
                'Type'   => (string) $this->mover_type,
                'Speed'  => (float) $this->mover_speed,
                'Height' => (float) $this->mover_height,
            ],
        ];
    }
}
