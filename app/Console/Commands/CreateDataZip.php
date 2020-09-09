<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

/**
 * Class CreateDataZip
 *
 * @package App\Console\Commands
 */
class CreateDataZip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:zip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create data zip.';

    public function handle(): void
    {
        foreach (Route::getRoutes() as $route) {
            /** @var \Illuminate\Routing\Route $route */

            if ($route->getPrefix() !== 'api') {
                continue;
            }

            $fileName = str_replace('api/', '', $route->uri) . '.json';
            $fileContents = file_get_contents('http://127.0.0.1:8000/' . $route->uri);

            if (strpos($fileName, 'i18n/') === false) {
                $fileName = 'data/' . $fileName;
            }

            $this->info($fileName);

            if (!File::isDirectory(public_path('data/data'))) {
                File::makeDirectory(public_path('data/data'));
            }

            if (!File::isDirectory(public_path('data/i18n'))) {
                File::makeDirectory(public_path('data/i18n'));
            }

            File::put(public_path('data' . DIRECTORY_SEPARATOR . $fileName), $fileContents);
        }
    }
}
