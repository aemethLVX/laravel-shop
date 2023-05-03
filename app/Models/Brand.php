<?php

namespace App\Models;

use App\Traits\Models\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;
    use HasSlug;

    protected $fillable = [
        'slug',
        'title',
        'picture',
        'show_on_main_page',
        'sort',
    ];

    public function scopeShowOnMainPage(Builder $builder)
    {
        $builder->where('show_on_main_page', true)
            ->orderBy('sort')
            ->limit(6);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
