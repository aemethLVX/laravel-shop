<?php

namespace Database\Factories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brand>
 */
class BrandFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->company(),
            'picture' => $this->faker->loremFlickrImage('images/brands', 600, 600, 'brand_logo'),
            'show_on_main_page' => $this->faker->boolean,
            'sort' => $this->faker->numberBetween(1, 500),
        ];
    }
}
