<?php

declare(strict_types=1);

namespace App\Models;

use App\Presentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class ItemRarity extends Model implements Presentable
{
    public const TYPES = [
        'Junk',
        'Common',
        'Magic',
        'Rare',
        'Unique',
        'Legendary',
        'Mythic',
        'Lore',
        'Blizzard',
    ];

    /**
     * @param Request    $request
     * @param ItemRarity $rarity
     *
     * @return ItemRarity
     */
    public static function populate(ItemRarity $rarity, Request $request): self
    {
        $rarity->index = (int) $request->index;
        $rarity->name_i18n_id = $request->name_i18n_id;
        $rarity->type = $request->type;
        $rarity->color_code = $request->color_code;
        $rarity->save();

        return $rarity;
    }

    /**
     * @inheritdoc
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = 'item_rarities';
        $this->with = ['nameI18n'];
        $this->timestamps = false;
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
        return [
            'Id'        => $this->id,
            'NameKey'   => $this->nameI18n->key,
            'Type'      => $this->type,
            'ColorCode' => $this->color_code,
        ];
    }

}
