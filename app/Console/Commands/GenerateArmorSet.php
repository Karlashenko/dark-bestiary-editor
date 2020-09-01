<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\File;
use App\Models\I18N;
use App\Models\Item;
use App\Models\ItemCategory;
use App\Models\ItemType;
use App\Models\Price;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateArmorSet
 *
 * @package App\Console\Commands
 */
class GenerateArmorSet extends Command
{
    private const RARITY_ID_JUNK         = 1;
    private const RARITY_ID_COMMON       = 2;
    private const RARITY_ID_MAGIC        = 7;
    private const RARITY_ID_RARE         = 3;
    private const RARITY_ID_UNIQUE       = 4;
    private const RARITY_ID_LEGENDARY    = 5;

    private const ITEM_MOD_ID_LIGHT_ARMOR  = 38;
    private const ITEM_MOD_ID_MEDIUM_ARMOR = 39;
    private const ITEM_MOD_ID_HEAVY_ARMOR  = 40;

    protected $signature = 'generate:armor';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        return;

        $items = [
            'Helmet' => [
                'en' => 'Chainmail Helm',
                'ru' => 'Кольчужный шлем',
            ],
            'Armor' => [
                'en' => 'Chainmail Vest',
                'ru' => 'Кольчужный нагрудник',
            ],
            'Shoulders' => [
                'en' => 'Chainmail Shoulders',
                'ru' => 'Кольчужные наплечники',
            ],
            'Belt' => [
                'en' => 'Chainmail Belt',
                'ru' => 'Кольчужный пояс',
            ],
            'Pants' => [
                'en' => 'Chainmail Pants',
                'ru' => 'Кольчужные штаны',
            ],
            'Gloves' => [
                'en' => 'Chainmail Gloves',
                'ru' => 'Кольчужные перчатки',
            ],
            'Boots' => [
                'en' => 'Chainmail Boots',
                'ru' => 'Кольчужные сапоги',
            ],
        ];

        DB::transaction(function () use ($items) {
            foreach ($items as $itemType => $itemName) {
                $item = new Item();
                $item->rarity_id = self::RARITY_ID_MAGIC;
                $item->type_id = ItemType::whereType($itemType)->first()->id;
                $item->name_i18n_id = $this->createI18N($itemName)->id;
                $item->flags = ['HasRandomSuffix', 'Dismantable'];
                $item->icon = '';
                $item->stack_size = 0;
                $item->required_level = 0;
                $item->level = 8;
                $item->is_enabled = true;
                $item->save();
                $item->itemModifiers()->attach(self::ITEM_MOD_ID_MEDIUM_ARMOR);

                $this->info('--- ' . $itemType . ' ---');
                $this->info($item->nameI18n->key);
                $this->info($item->nameI18n->en);
                $this->info($item->nameI18n->ru);
            }
        });
    }

    private function createI18N(array $name): I18N
    {
        $i18n = new I18N();
        $i18n->en = $name['en'];
        $i18n->ru = $name['ru'];
        $i18n->key = preg_replace('/[^A-Za-z ]+/', '', $name['en']);
        $i18n->key = str_replace(' ', '_', $i18n->key);
        $i18n->key = mb_strtolower($i18n->key);
        $i18n->key = 'item_' . $i18n->key . '_name';
        $i18n->save();

        return $i18n;
    }
}
