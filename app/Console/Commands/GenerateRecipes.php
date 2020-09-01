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
class GenerateRecipes extends Command
{
    private const RARITY_ID_JUNK      = 1;
    private const RARITY_ID_COMMON    = 2;
    private const RARITY_ID_MAGIC     = 7;
    private const RARITY_ID_RARE      = 3;
    private const RARITY_ID_UNIQUE    = 4;
    private const RARITY_ID_LEGENDARY = 5;

    private const INGREDIENT_SCRAP = 21;
    private const INGREDIENT_DUST  = 141;

    protected $signature = 'generate:recipes';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            foreach (Item::get() as $item) {
                if (!in_array('Craftable', $item->flags)) {
                    continue;
                }

                $recipe = Recipe::where('item_id', $item->id)->first();

                $new = false;

                if ($recipe === null) {
                    $new = true;
                    $recipe = new Recipe();
                }

                if ($recipe->is_custom) {
                    continue;
                }

                $recipe->item_id = $item->id;
                $recipe->item_count = 1;
                $recipe->save();

                $recipe->ingredients()->sync([]);
                $recipe->ingredients()->attach(self::INGREDIENT_SCRAP, ['count' => $this->getScrapCount($item)]);
                $recipe->ingredients()->attach(self::INGREDIENT_DUST, ['count' => $this->getDustCount($item)]);

                if ($new) {
                    $this->info($item->nameI18n->en);
                    $this->info('   Scrap: ' . $this->getScrapCount($item));
                    $this->info('   Dust:  ' . $this->getDustCount($item));
                }
            }
        });
    }

    private function getScrapCount(Item $item): int
    {
        return (int) ($item->level * 10 * $this->getQualityMultiplier($item));
    }

    private function getDustCount(Item $item): int
    {
        return (int) ($item->level * 2 * $this->getQualityMultiplier($item));
    }

    private function getQualityMultiplier(Item $item): float
    {
        $fraction = 1;

        if ($item->isTwoHandedWeapon() ||  $item->isArmor()) {
            $fraction = 2;
        }

        return $fraction;
    }
}
