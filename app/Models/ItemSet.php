<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemSet extends Model implements Presentable
{
    /**
     * @param ItemSet $itemSet
     * @param Request $request
     *
     * @return ItemSet
     * @throws \Throwable
     */
    public static function populate(ItemSet $itemSet, Request $request): self
    {
        DB::transaction(function () use ($itemSet, $request) {
            $itemSet->name_i18n_id = $request->name_i18n_id;
            $itemSet->save();

            $itemSet->behaviours()->sync([]);

            foreach ((array) $request->get('behaviours', []) as $behaviour) {
                $itemSet->behaviours()->attach(
                    (int) \array_get($behaviour, 'id'),
                    ['item_count' => (int) \array_get($behaviour, 'pivot.item_count')]
                );
            }

            $itemSet->load($itemSet->with);
        });

        return $itemSet;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'item_sets';
        $this->timestamps = false;
        $this->with = ['behaviours', 'nameI18n'];
    }

    /**
     * @return BelongsToMany
     */
    public function behaviours(): BelongsToMany
    {
        return $this->belongsToMany(Behaviour::class, 'item_sets_behaviours')
                    ->withPivot(['id', 'item_count'])
                    ->orderBy('pivot_id');
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'set_id');
    }

    /**
     * @return BelongsTo
     */
    public function nameI18n(): BelongsTo
    {
        return $this->belongsTo(I18N::class, 'name_i18n_id')->withDefault();
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        $behaviours = $this->behaviours->groupBy(function (Behaviour $behaviour) {return $behaviour->pivot->item_count;})
                                       ->map(function ($group, $key) {
                                           return [
                                               'ItemCount'  => (int) $key,
                                               'Behaviours' => $group->map(function ($behaviour) {return $behaviour->id;}),
                                           ];
                                       })->values();

        return [
            'Id'         => $this->id,
            'NameKey'    => $this->nameI18n->key,
            'Items'      => $this->items->pluck('id')->toArray(),
            'Behaviours' => $behaviours,
        ];
    }
}
