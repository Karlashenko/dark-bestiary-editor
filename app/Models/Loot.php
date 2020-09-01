<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use App\Http\Requests\LootRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Loot
 *
 * @package App\Models
 */
class Loot extends Model implements Presentable
{
    /**
     * @param Loot        $loot
     * @param LootRequest $request
     *
     * @return Loot
     * @throws \Exception
     * @throws \Throwable
     */
    public static function populate(Loot $loot, LootRequest $request): self
    {
        DB::transaction(function () use ($loot, $request) {
            $loot->name = (string) $request->name;
            $loot->count = (int) $request->count;
            $loot->save();

            $loot->items()->delete();

            foreach ((array) $request->get('items', []) as $data) {
                $item = new LootItem();
                $item->type = (string) \array_get($data, 'type');
                $item->loot_id = (int) $loot->id;
                $item->item_id = \array_get($data, 'item_id');
                $item->table_id = \array_get($data, 'table_id');
                $item->category_id = \array_get($data, 'category_id');
                $item->rarity_id = \array_get($data, 'rarity_id');
                $item->level_min = (int) \array_get($data, 'level_min');
                $item->level_max = (int) \array_get($data, 'level_max');
                $item->stack_count_min = (int) \array_get($data, 'stack_count_min');
                $item->stack_count_max = (int) \array_get($data, 'stack_count_max');
                $item->probability = (float) \array_get($data, 'probability');
                $item->is_unique = (bool) \array_get($data, 'is_unique');
                $item->is_guaranteed = (bool) \array_get($data, 'is_guaranteed');
                $item->is_enabled = (bool) \array_get($data, 'is_enabled');
                $item->save();
            }

            $loot->load($loot->with);
        });

        return $loot;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'loot';
        $this->with = ['items'];
    }

    /**
     * @return HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(LootItem::class, 'loot_id')
                    ->select('loot_items.*')
                    ->join('items as items', 'items.id', '=', 'loot_items.item_id', 'left')
                    ->join('i18n as i18n', 'items.name_i18n_id', '=', 'i18n.id', 'left')
                    ->orderBy('probability')
                    ->orderBy('type')
                    ->orderBy('i18n.en')
                    ->orderBy('table_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'    => $this->id,
            'Name'  => $this->name,
            'Count' => $this->count,
            'Items' => $this->items->present(),
        ];
    }
}
