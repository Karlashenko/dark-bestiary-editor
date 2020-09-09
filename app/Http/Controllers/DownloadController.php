<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use SplFileInfo;
use ZipArchive;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class AnalyticsController
 *
 * @package App\Http\Controllers
 */
class DownloadController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $rootPath = realpath(public_path('data'));
        $filename = 'mod.zip';

        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        /** @var SplFileInfo[] $files */
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($rootPath), \RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                $zip->addFile($filePath, $relativePath);
            }
        }

        $zip->close();

        return response()->download(public_path($filename));
    }
}
