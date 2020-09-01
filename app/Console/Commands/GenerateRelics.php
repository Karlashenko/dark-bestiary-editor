<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Relic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateRelics
 *
 * @package App\Console\Commands
 */
class GenerateRelics extends Command
{
    protected $signature = 'generate:relics';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        DB::transaction(function () {
            foreach (Relic::get() as $relic) {

                $item = Item::where('unlock_relic_id', $relic->id)->first();

                $new = $item === null;

                if ($new) {
                    $item = new Item();
                }

                $item->is_enabled = true;
                $item->type_id = 62;
                $item->unlock_relic_id = $relic->id;
                $item->rarity_id = $relic->rarity_id;
                $item->name_i18n_id = $relic->name_i18n_id;
                $item->lore_i18n_id = $relic->lore_i18n_id;
                $item->passive_description_i18n_id = $relic->description_i18n_id;
                $item->consume_description_i18n_id = 2364;
                $item->icon = $relic->icon;
                $item->flags = $item->flags ?? ['Droppable'];
                $item->consume_sound = 'event:/SFX/UI/Skill_Unlock';
                $item->stack_size = 0;
                $item->level = 2;
                $item->required_level = 0;
                $item->save();

                if ($new) {
                    $this->info($item->nameI18n->en);
                }
            }
        });
    }
}
