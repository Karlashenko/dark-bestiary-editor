<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Recipe;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateRecipes
 *
 * @package App\Console\Commands
 */
class GenerateBlueprints extends Command
{
    private const RARITY_ID_JUNK         = 1;
    private const RARITY_ID_COMMON       = 2;
    private const RARITY_ID_MAGIC        = 7;
    private const RARITY_ID_RARE         = 3;
    private const RARITY_ID_UNIQUE       = 4;
    private const RARITY_ID_LEGENDARY    = 5;

    private const TYPE_ID_BLUEPRINT = 61;

    protected $signature = 'generate:blueprints';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            foreach (Item::get() as $item) {
                if (!in_array('Craftable', $item->flags) || !in_array('HasBlueprint', $item->flags)) {
                    continue;
                }

                $recipe = Recipe::where('item_id', $item->id)->first();

                if ($recipe === null) {
                    $this->warn($item->nameI18n->en . ' has no recipe.');
                    continue;
                }

                $blueprint = $recipe->blueprint;

                if ($blueprint === null) {
                    $blueprint = new Item();

                    $this->info('Created blueprint for: ' . $recipe->item->nameI18n->en);
                }

                $blueprint->is_enabled = true;
                $blueprint->type_id = self::TYPE_ID_BLUEPRINT;
                $blueprint->rarity_id = $item->rarity_id;
                $blueprint->name_i18n_id = $item->name_i18n_id;
                $blueprint->icon = $this->getBlueprintIcon($item);
                $blueprint->flags = ['Droppable'];
                $blueprint->stack_size = 0;
                $blueprint->level = $item->level;
                $blueprint->required_level = 0;
                $blueprint->save();

                $recipe->blueprint_id = $blueprint->id;
                $recipe->save();

                $recipe->load('blueprint');
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
