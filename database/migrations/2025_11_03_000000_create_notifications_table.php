<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable();
            $table->string('title');
            $table->string('href')->nullable();
            $table->string('button_name')->nullable();
            $table->boolean('status')->default(true); // active/inactive
            $table->boolean('featured')->default(false);
            $table->boolean('feature_on_top')->default(false);
            $table->date('display_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};


