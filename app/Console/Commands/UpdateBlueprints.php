<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateRecipes
 *
 * @package App\Console\Commands
 */
class UpdateBlueprints extends Command
{
    private const RARITY_ID_JUNK         = 1;
    private const RARITY_ID_COMMON       = 2;
    private const RARITY_ID_MAGIC        = 7;
    private const RARITY_ID_RARE         = 3;
    private const RARITY_ID_UNIQUE       = 4;
    private const RARITY_ID_LEGENDARY    = 5;

    private const TYPE_ID_BLUEPRINT = 61;

    protected $signature = 'update:blueprints';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            foreach (Item::whereTypeId(self::TYPE_ID_BLUEPRINT)->get() as $item) {
                $item->level = $item->blueprintRecipe->item->level;
                $item->rarity_id = $item->blueprintRecipe->item->rarity_id;
                $item->icon = $this->getBlueprintIcon($item->blueprintRecipe->item);
                $item->consume_sound = 'event:/SFX/UI/Skill_Unlock';
                $item->save();

                echo $item->nameI18n->en . ' ' . $item->level . PHP_EOL;
            }
        });
    }

    private function getBlueprintIcon(Item $item): string
    {
        if ($item->rarity_id === self::RARITY_ID_MAGIC) {
            return 'Sprites/Icons/Items/icon_blueprint_magic';
        }

        if ($item->rarity_id === self::RARITY_ID_RARE) {
            return 'Sprites/Icons/Items/icon_blueprint_rare';
        }

        if ($item->rarity_id === self::RARITY_ID_UNIQUE) {
            return 'Sprites/Icons/Items/icon_blueprint_unique';
        }

        return '';
    }
}
