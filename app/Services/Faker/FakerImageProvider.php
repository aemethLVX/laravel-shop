<?php

namespace App\Services\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FakerImageProvider extends Base
{
    public function loremFlickrImage(
        string $directory,
        int $width = 600,
        int $height = 600,
        string $type = ''
    ): string {
        $name = $directory . '/' . Str::random(6) . '.jpg';
        $type = !$type ?: '/' . $type;

        Storage::put(
            $name,
            file_get_contents("https://loremflickr.com/{$width}/{$height}{$type}")
        );

        return '/storage/' . $name;
    }

    public function fixturesImage(
        string $fixturesDirectory,
        string $storageDirectory,
    ): string {
        if (!Storage::exists($storageDirectory)) {
            Storage::makeDirectory($storageDirectory);
        }

        $file = $this->generator->file(
            base_path("tests/Fixtures/images/{$fixturesDirectory}"),
            Storage::path($storageDirectory),
            false
        );

        return '/storage/' . trim($storageDirectory) . '/' . $file;
    }
}
