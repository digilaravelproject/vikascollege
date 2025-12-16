<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // otp_email, admission_admin, admission_student, enquiry_admin, enquiry_student
            $table->string('subject')->nullable();
            $table->text('html')->nullable();
            $table->timestamps();
        });

        // seed sensible defaults
        DB::table('mail_templates')->insert([
            ['key' => 'otp_email', 'subject' => 'Your OTP', 'html' => 'Hello, your OTP is: {{otp}}'],
            ['key' => 'admission_admin', 'subject' => 'New Admission Application', 'html' => 'New application: {{name}} - {{email}}'],
            ['key' => 'admission_student', 'subject' => 'Application Received', 'html' => 'Thanks {{name}}. We received your application.'],
            ['key' => 'enquiry_admin', 'subject' => 'New Enquiry', 'html' => 'New enquiry: {{name}} - {{email}} - {{message}}'],
            ['key' => 'enquiry_student', 'subject' => 'Enquiry Received', 'html' => 'Thanks {{name}}. We received your enquiry.']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_templates');
    }
};
