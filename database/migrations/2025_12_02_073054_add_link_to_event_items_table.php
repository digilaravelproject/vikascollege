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
        Schema::table('event_items', function (Blueprint $table) {
            // Adding link after venue, nullable because not all events might have a link
            $table->string('link')->nullable()->after('venue');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_items', function (Blueprint $table) {
            $table->dropColumn('link');
        });
    }
};
