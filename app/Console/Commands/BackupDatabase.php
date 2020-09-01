<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class
 *
 * @package App\Console\Commands
 */
class BackupDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    public function handle(): void
    {
        \exec('pg_dump -U ' . \env('DB_USERNAME') . ' -d ' . \env('DB_DATABASE') . ' -h ' . \env('DB_HOST') . ' -Fc > ' . \base_path() . '/database/backup/'. 'pg_dump_' . \date('Y-m-d') . '.dump');
    }
}
