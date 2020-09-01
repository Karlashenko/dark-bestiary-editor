<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Price;
use Illuminate\Console\Command;

/**
 * Class GenerateItemPrices
 *
 * @package App\Console\Commands
 */
class GenerateItemPrices extends Command
{
    private const CURRENCY_ID_GOLD       = 1;
    private const CATEGORY_ID_TWO_HANDED = 6;

    private const TYPE_ID_ACCESSORY      = 22;
    private const TYPE_ID_CHEST          = 25;
    private const TYPE_ID_INGREDIENT     = 21;
    private const TYPE_ID_CONSUMABLE     = 27;
    private const TYPE_ID_RING           = 18;
    private const TYPE_ID_AMULET         = 19;
    private const TYPE_ID_GEM            = 20;
    private const TYPE_ID_CURRENCY       = 36;
    private const TYPE_ID_RELIC          = 62;
    private const TYPE_ID_ENCHANTMENT    = 65;

    private const RARITY_ID_JUNK         = 1;
    private const RARITY_ID_COMMON       = 2;
    private const RARITY_ID_MAGIC        = 7;
    private const RARITY_ID_RARE         = 3;
    private const RARITY_ID_UNIQUE       = 4;
    private const RARITY_ID_LEGENDARY    = 5;

    protected $signature = 'generate:item_prices';

    public function handle(): void
    {
        foreach (Item::get() as $item) {
            $item->prices()->delete();

            $price = new Price();
            $price->priceable_id = $item->id;
            $price->priceable_type = \get_class($item);
            $price->currency_id = self::CURRENCY_ID_GOLD;
            $price->amount = (int) $this->GetPrice($item);
            $price->save();

            $this->info($item->nameI18n->en . ': ' . $price->amount . 'g');
        }
    }

    private function GetPrice(Item $item): float
    {
        return max(1, $item->level) * 60 * $this->GetQualityMultiplier($item) * $this->GetTypeMultiplier($item);
    }

    private function GetQualityMultiplier(Item $item): float
    {
        switch ($item->rarity_id)
        {
            case self::RARITY_ID_JUNK: return 0.05;
            case self::RARITY_ID_COMMON: return 1;
            case self::RARITY_ID_MAGIC: return 3;
            case self::RARITY_ID_RARE: return 4;
            case self::RARITY_ID_UNIQUE: return 5;
            case self::RARITY_ID_LEGENDARY: return 9;
            default: return 0.05;
        }
    }

    private function GetTypeMultiplier(Item $item): float
    {
        if (ItemCategory::find(self::CATEGORY_ID_TWO_HANDED)->types->contains(function (ItemType $type) use ($item) {
            return $type->id === $item->type_id; })) {
            return 2;
        }

        if ($item->type_id === self::TYPE_ID_CHEST) {
            return 2;
        }

        if ($item->type_id === self::TYPE_ID_ACCESSORY || $item->type_id === self::TYPE_ID_RING ||
            $item->type_id === self::TYPE_ID_AMULET) {
            return 3;
        }

        if ($item->type_id === self::TYPE_ID_RELIC) {
            return 4;
        }

        if ($item->type_id === self::TYPE_ID_ENCHANTMENT) {
            return 5;
        }

        if ($item->consume_loot_id > 0) {
            return 3;
        }

        if ($item->type_id === self::TYPE_ID_GEM) {
            return 1.5;
        }

        if ($item->type_id === self::TYPE_ID_INGREDIENT) {
            return 0.05;
        }

        if ($item->type_id === self::TYPE_ID_CURRENCY) {
            return 0;
        }

        return 1.0;
    }
}
