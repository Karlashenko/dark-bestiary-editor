<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use ZipArchive;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
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
        $zip = new ZipArchive;
        $fileName = 'data.zip';

        if ($zip->open(public_path($fileName), ZipArchive::CREATE) === true)
        {
            $files = File::files(public_path('data'));

            foreach ($files as $value) {
                $relativeNameInZipFile = $value->getBasename();
                $zip->addFile($value->getPathname(), $relativeNameInZipFile);
            }

            $zip->close();
        }

        return response()->download(public_path($fileName));
    }
}
