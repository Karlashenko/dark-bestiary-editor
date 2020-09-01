<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Background extends Model implements Presentable
{
    /**
     * @param Background $background
     * @param Request    $request
     *
     * @return Background
     * @throws \Throwable
     */
    public static function populate(Background $background, Request $request): self
    {
        DB::transaction(function () use ($background, $request) {
            $background->name_i18n_id = $request->name_i18n_id;
            $background->description_i18n_id = $request->description_i18n_id;
            $background->is_enabled = (bool) $request->is_enabled;
            $background->gold = (int) $request->gold;
            $background->save();

            $background->items()->sync([]);

            foreach ((new Collection((array) $request->get('items', [])))->unique('id') as $data) {
                $background->items()->attach(
                    (int) \array_get($data, 'id'),
                    [
                        'count'       => (int) \array_get($data, 'pivot.count'),
                        'is_equipped' => (bool) \array_get($data, 'pivot.is_equipped'),
                    ]);
            }

            $background->skills()->sync([]);

            foreach ((new Collection((array) $request->get('skills', [])))->unique('id') as $data) {
                $background->skills()->attach((int) \array_get($data, 'id'));
            }
        });

        $background->load($background->with);

        return $background;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        $this->table = 'backgrounds';
        $this->timestamps = false;
        $this->with = ['nameI18n', 'descriptionI18n', 'items', 'skills'];

        parent::__construct($attributes);
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
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'backgrounds_items')
                    ->withPivot(['id', 'count', 'is_equipped'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return BelongsToMany
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'backgrounds_skills')
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
            'NameKey'        => $this->nameI18n->key,
            'DescriptionKey' => $this->descriptionI18n->key,
            'Gold'           => (int) $this->gold,

            'Skills' => $this->skills()->get()->map(function (Skill $skill) {
                return $skill->id;
            })->toArray(),

            'Items' => $this->items()->get()->map(function (Item $item) {
                return [
                    'ItemId'     => $item->id,
                    'Count'      => $item->pivot->count,
                    'IsEquipped' => (bool) $item->pivot->is_equipped,
                ];
            }),
        ];
    }
}
