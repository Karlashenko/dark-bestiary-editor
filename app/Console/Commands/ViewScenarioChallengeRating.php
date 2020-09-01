<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Episode;
use App\Models\Item;
use App\Models\Scenario;
use App\Models\Unit;
use App\Models\UnitTable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateItemLevels
 *
 * @package App\Console\Commands
 */
class ViewScenarioChallengeRating extends Command
{
    protected $signature = 'test:cr';

    /**
     * @throws \Throwable
     */
    public function handle(): void
    {
        foreach (Scenario::where('type', 'Campaign')->orderBy('index')->get() as $scenario) {
            echo $scenario->nameI18n->en . PHP_EOL;

            foreach ($scenario->episodes as $episode) {
                /**
                 * @var Episode $episode
                 */

                $rating = 0;

                foreach ($episode->encounter->unitTable->units as $unit) {
                    $rating += $unit->unit->challenge_rating;
                }

                echo '  ' . $rating . PHP_EOL;
            }
        }
    }
}
