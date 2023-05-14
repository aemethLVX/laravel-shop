<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Brand::factory(10)->create();
        Category::factory(10)
            ->has(Product::factory(rand(5, 10)))
            ->create();
        User::factory(5)->create();
    }
}
