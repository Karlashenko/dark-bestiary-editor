<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Price;
use App\Models\Skill;
use Illuminate\Console\Command;

/**
 * Class GenerateSkillPrices
 *
 * @package App\Console\Commands
 */
class GenerateSkillPrices extends Command
{
    private const RARITY_ID_JUNK         = 1;
    private const RARITY_ID_COMMON       = 2;
    private const RARITY_ID_MAGIC        = 7;
    private const RARITY_ID_RARE         = 3;
    private const RARITY_ID_UNIQUE       = 4;
    private const RARITY_ID_LEGENDARY    = 5;
    private const CURRENCY_ID_GOLD       = 1;

    protected $signature = 'generate:skill_prices';

    public function handle(): void
    {
        foreach (Skill::get() as $skill) {
            $skill->prices()->delete();

            $price = new Price();
            $price->priceable_id = $skill->id;
            $price->priceable_type = \get_class($skill);
            $price->currency_id = self::CURRENCY_ID_GOLD;
            $price->amount = (int) $this->GetPrice($skill);
            $price->save();

            $this->info($skill->nameI18n->en . ': ' . $price->amount . 'g');
        }
    }

    private function GetPrice(Skill $skill): float
    {
        return pow(228, $this->GetQualityMultiplier($skill)) + rand(1, 6);
    }

    private function GetQualityMultiplier(Skill $skill): float
    {
        switch ($skill->rarity_id)
        {
            case self::RARITY_ID_JUNK: return 1;
            case self::RARITY_ID_COMMON: return 1;
            case self::RARITY_ID_MAGIC: return 1.05;
            case self::RARITY_ID_RARE: return 1.1;
            case self::RARITY_ID_UNIQUE: return 1.15;
            case self::RARITY_ID_LEGENDARY: return 1.3;
        }

        return 0;
    }
}
