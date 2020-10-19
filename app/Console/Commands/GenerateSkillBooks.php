<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Relic;
use App\Models\Skill;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateRelics
 *
 * @package App\Console\Commands
 */
class GenerateSkillBooks extends Command
{
    protected $signature = 'generate:skillbooks';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        // Item::whereTypeId(68)->delete();

        DB::transaction(function () {
            foreach (Skill::where('is_enabled', true)->where('type', 'Common')->get() as $skill) {
                if (in_array('Monster', $skill->flags) || in_array('Item', $skill->flags)) {
                    continue;
                }

                $item = Item::where('learn_skill_id', $skill->id)->first();

                $new = $item === null;

                if ($new) {
                    $item = new Item();
                }

                $item->is_enabled = true;
                $item->type_id = 68;
                $item->learn_skill_id = $skill->id;
                $item->rarity_id = $skill->rarity_id;
                $item->name_i18n_id = $skill->name_i18n_id;
                $item->consume_description_i18n_id = 3675;
                $item->icon = $skill->icon;
                $item->flags = ['Droppable'];
                $item->consume_sound = 'event:/SFX/UI/Skill_Unlock';
                $item->stack_size = 0;
                $item->level = 1;
                $item->required_level = 0;
                $item->save();

                if ($new) {
                    $this->info($item->nameI18n->en);
                }
            }
        });
    }
}
