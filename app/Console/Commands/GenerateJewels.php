<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\I18N;
use App\Models\Item;
use App\Models\ItemModifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class GenerateRecipes
 *
 * @package App\Console\Commands
 */
class GenerateJewels extends Command
{
    private const RARITY_ID_UNIQUE    = 4;
    private const RARITY_ID_LEGENDARY = 5;
    private const ITEM_TYPE_ID_GEM    = 20;

    protected $signature = 'generate:jewels';

    /**
     * @throws Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            Item::where('rarity_id', self::RARITY_ID_LEGENDARY)
                ->where('type_id', self::ITEM_TYPE_ID_GEM)
                ->whereNull('suffix_id')
                ->delete();

            $suffixes = ItemModifier::whereIsSuffix(true)->get();
            $name = I18N::where('key', 'item_jewel_name')->first();

            foreach ($suffixes as $suffix) {
                $jewel = Item::whereSuffixId($suffix->id)->firstOrNew([]);
                $jewel->is_enabled = true;
                $jewel->type_id = self::ITEM_TYPE_ID_GEM;
                $jewel->rarity_id = self::RARITY_ID_LEGENDARY;
                $jewel->suffix_id = $suffix->id;
                $jewel->name_i18n_id = $name->id;
                $jewel->icon = $this->getIcon($suffix);
                $jewel->stack_size = 999999;
                $jewel->level = 2;
                $jewel->required_level = 0;
                $jewel->flags = ['Stackable'];
                $jewel->save();

                $this->info("Created item: {$jewel->nameI18n->en} {$suffix->suffixI18n->en}");
            }
        });
    }

    /**
     * @param ItemModifier $suffix
     *
     * @return string
     */
    private function getIcon(ItemModifier $suffix): string
    {
        if ($suffix->itemModifiers->count() > 2) {
            return 'Sprites/Icons/Items/icon_gem_jewel_01';
        } else {
            return 'Sprites/Icons/Items/icon_gem_jewel_02';
        }
    }
}
