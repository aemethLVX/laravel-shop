<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('show_on_main_page')->default(false);
            $table->integer('sort')->default(500);
        });
    }

    public function down(): void
    {
        if (!app()->isProduction()) {
            Schema::table('categories', function (Blueprint $table) {
                //
            });
        }
    }
};
