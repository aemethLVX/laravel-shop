<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ThumbnailController extends Controller
{
    public function __invoke(
        string $directory,
        string $method,
        string $size,
        string $file
    ): BinaryFileResponse {
        abort_if(
            !in_array($size, config('thumbnail.allowed_sizes')),
            403,
            'Size is not allowed'
        );

        $storage = Storage::disk('images');

        $realPath = "{$directory}/{$file}";
        $newDirPath = "{$directory}/{$method}/{$size}";
        $resultPath = "{$newDirPath}/{$file}";

        if (!$storage->exists($newDirPath)) {
            $storage->makeDirectory($newDirPath);
        }

        if (!$storage->exists($resultPath)) {
            $image = Image::make($storage->path($realPath));

            [$w, $h] = explode('x', $size);

            $image->{$method}($w, $h);
            $image->save($storage->path($resultPath));
        }

        return response()->file($storage->path($resultPath));
    }
}
