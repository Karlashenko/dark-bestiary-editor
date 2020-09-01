<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * Class UploadController
 */
class FilesController extends Controller
{
    /**
     * @return JsonResponse
     * @throws \Throwable
     */
    public function upload(): JsonResponse
    {
        $response = (object) [];

        DB::transaction(function () use (&$response) {
            foreach ($_FILES as $data) {
                $filename = time() . '_' . $data['name'];
                $destination = \public_path('uploads') . DIRECTORY_SEPARATOR . $filename;
                \move_uploaded_file($data['tmp_name'], $destination);

                $file = new File();
                $file->mime = $data['type'];
                $file->size = $data['size'];
                $file->path = $destination;
                $file->name = $filename;
                $file->url = \str_replace(DIRECTORY_SEPARATOR, '/', \str_replace(\public_path(), '', $destination));
                $file->save();

                $response = $file;
            }
        });

        return \response()->json($response);
    }

    /**
     * @param File $file
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function delete(File $file): JsonResponse
    {
        $file->delete();

        return \response()->json((object) []);
    }
}
