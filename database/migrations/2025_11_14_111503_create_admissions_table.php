<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->string('mobile_prefix')->nullable();
            $table->string('mobile_no')->nullable();
            $table->string('discipline')->nullable();
            $table->string('level')->nullable();
            $table->string('programme')->nullable();
            $table->boolean('authorised_contact')->default(false);
            $table->string('status')->default('new'); // new, submitted, processed
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};
