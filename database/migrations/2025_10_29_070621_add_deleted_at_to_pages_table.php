<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('pages', 'deleted_at')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        if (!Schema::hasColumn('menus', 'deleted_at')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pages', 'deleted_at')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }

        if (Schema::hasColumn('menus', 'deleted_at')) {
            Schema::table('menus', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
