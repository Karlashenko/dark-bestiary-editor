<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class CleanupFiles
 *
 * @package App\Console\Commands
 */
class CleanupFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete files than not present in fileables table.';

    public function handle(): void
    {
        $deleted = 0;

        $inFileables = DB::table('fileables')->select('file_id')->get()->map(function ($row) {
            return $row->file_id;
        })->toArray();

        $files = File::whereNotIn('id', $inFileables)->get();

        foreach ($files as $file) {
            $this->info($file->path);
            $file->delete();
        }

        $deleted += $files->count();

        $files = File::get();

        foreach ($files as $file) {
            if (\file_exists($file->path)) {
                continue;
            }

            $deleted++;
            $this->info($file->path);
            $file->delete();
        }

        $this->info('Files deleted: ' . $deleted);
    }
}
