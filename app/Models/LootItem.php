<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class LootItem
 *
 * @package App\Models
 */
class LootItem extends Model implements Presentable
{
    public const TYPES = [
        'Null',
        'Item',
        'Table',
        'Random',
    ];

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'loot_items';
    }

    /**
     * @return BelongsTo
     */
    public function loot(): BelongsTo
    {
        return $this->belongsTo(Loot::class, 'loot_id');
    }

    /**
     * @return BelongsTo
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * @inheritdoc
     */
    public function present(): array
    {
        return [
            'Id'          => $this->id,
            'Type'        => $this->type,
            'ItemId'      => (int) $this->item_id,
            'TableId'     => (int) $this->table_id,
            'RarityId'    => (int) $this->rarity_id,
            'CategoryId'  => (int) $this->category_id,
            'LevelMin'    => (int) $this->level_min,
            'LevelMax'    => (int) $this->level_max,
            'Probability' => (float) $this->probability,
            'StackMin'    => (int) $this->stack_count_min,
            'StackMax'    => (int) $this->stack_count_max,
            'Enabled'     => (bool) $this->is_enabled,
            'Unique'      => (bool) $this->is_unique,
            'Guaranteed'  => (bool) $this->is_guaranteed,
            'IgnoreLevel' => (bool) $this->is_ignore_level,
        ];
    }

}
