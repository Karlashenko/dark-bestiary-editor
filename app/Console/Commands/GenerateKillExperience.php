<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Unit;
use Illuminate\Console\Command;

/**
 * Class CleanupFiles
 *
 * @package App\Console\Commands
 */
class GenerateKillExperience extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:kill_experience';

    public function handle(): void
    {
        foreach (Unit::get() as $unit) {
            $unit->kill_experience = (int) ($unit->level * 2.5);
            $unit->save();
        }
    }
}
