<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(): Factory|View|Application
    {
        $categories = Category::query()->showOnMainPage()->get();
        $products = Product::query()->showOnMainPage()->get();
        $brands = Brand::query()->showOnMainPage()->get();

        return view(
            'index',
            compact(
                'categories',
                'products',
                'brands',
            )
        );
    }
}
