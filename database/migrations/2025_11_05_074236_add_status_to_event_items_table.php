<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            // Add 'status' column before 'meta_title'
            $table->boolean('status')->default(true)->after('full_content')->comment('1 = Published, 0 = Draft');
        });
    }

    public function down(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
