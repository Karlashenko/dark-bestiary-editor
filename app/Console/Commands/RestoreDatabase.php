<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class RestoreDatabase
 *
 * @package App\Console\Commands
 */
class RestoreDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:restore';

    public function handle(): void
    {
        $backups = \glob(\base_path() . '/database/backup/*.dump');
        $backupIndex = 0;

        if (count($backups) === 0) {
            $this->error('No backup files found');
            return;
        }

        $backup = $this->choice(PHP_EOL, $backups, $backupIndex);

        \exec('dropdb -U ' . \env('DB_USERNAME') . ' ' .  \env('DB_DATABASE'));
        \exec('createdb -U ' . \env('DB_USERNAME') . ' ' .  \env('DB_DATABASE'));
        \exec('pg_restore --no-owner --no-acl -h ' . \env('DB_HOST') . ' -d ' . \env('DB_DATABASE') . ' -U ' . \env('DB_USERNAME') . ' ' . $backup);
    }
}
