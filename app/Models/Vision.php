<?php

declare(strict_types=1);

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;
use Throwable;
use App\Presentable;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Vision
 *
 * @package App\Models
 */
class Vision extends Model implements Presentable
{
    public const TYPES = [
        'Vendor',
        'Combat',
        'Craft',
        'Dismantling',
    ];

    /**
     * @param Vision  $achievement
     * @param Request $request
     *
     * @return Vision
     * @throws Throwable
     * @throws Exception
     */
    public static function populate(Vision $vision, Request $request): self
    {
        DB::transaction(function () use ($vision, $request) {
            $vision->type = (string) $request->type;
            $vision->icon = (string) $request->icon;
            $vision->label = (string) $request->label;
            $vision->prefab = (string) $request->prefab;
            $vision->sound = (string) $request->sound;
            $vision->probability = (float) $request->probability;
            $vision->is_final = (bool) $request->is_final;
            $vision->is_guaranteed = (bool) $request->is_guaranteed;
            $vision->is_enabled = (bool) $request->is_enabled;
            $vision->is_unique = (bool) $request->is_unique;
            $vision->act_min = (int) $request->act_min;
            $vision->act_max = (int) $request->act_max;
            $vision->name_i18n_id = $request->name_i18n_id;
            $vision->description_i18n_id = $request->description_i18n_id;
            $vision->rarity_id = $request->rarity_id;
            $vision->loot_id = $request->loot_id;
            $vision->effect_id = $request->effect_id;
            $vision->scenario_id = $request->scenario_id;
            $vision->save();

            $vision->visions()->sync([]);

            foreach ((array) $request->get('visions', []) as $data) {
                $vision->visions()->attach((int) \array_get($data, 'id'));
            }
        });

        $vision->load($vision->with);

        return $vision;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'visions', 'rarity'];
    }

    /**
     * @return BelongsTo
     */
    public function rarity(): BelongsTo
    {
        return $this->belongsTo(ItemRarity::class, 'rarity_id')->withDefault();
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

    public function visions(): BelongsToMany
    {
        return $this->belongsToMany(__CLASS__, 'visions_visions', 'parent_id', 'child_id')
                    ->withPivot(['id'])
                    ->orderBy('pivot_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'             => $this->id,
            'NameKey'        => $this->namei18n->key,
            'DescriptionKey' => $this->descriptioni18n->key,
            'Type'           => $this->type,
            'Icon'           => $this->icon,
            'Label'          => $this->label,
            'Prefab'         => $this->prefab,
            'Sound'          => $this->sound,
            'Probability'    => (float) $this->probability,
            'IsFinal'        => (bool) $this->is_final,
            'IsGuaranteed'   => (bool) $this->is_guaranteed,
            'IsEnabled'      => (bool) $this->is_enabled,
            'IsUnique'       => (bool) $this->is_unique,
            'ActMin'         => (int) $this->act_min,
            'ActMax'         => (int) $this->act_max,
            'RarityId'       => (int) $this->rarity_id,
            'LootId'         => (int) $this->loot_id,
            'EffectId'       => (int) $this->effect_id,
            'ScenarioId'     => (int) $this->scenario_id,
            'Visions'        => $this->visions->map(function (Vision $vision) {return $vision->id;})->toArray(),
        ];
    }

}
