<?php

namespace App\Services\Faker;

use Faker\Provider\Base;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FakerImageProvider extends Base
{
    public function image(
        string $directory,
        int $width = 600,
        int $height = 600,
        string $type = ''
    ) {
        $name = $directory . '/' . Str::random(6) . '.jpg';
        $type = !$type ?: '/' . $type;

        Storage::put(
            $name,
            file_get_contents("https://loremflickr.com/{$width}/{$height}{$type}")
        );

        return '/storage/' . $name;
    }
}
